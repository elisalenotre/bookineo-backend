<?php
namespace App\Controller\Api;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/messages'), IsGranted('ROLE_USER')]
class MessageController extends AbstractController
{
    private function fmtDate(null|bool|string|\DateTimeInterface $v): ?string
    {
        if ($v instanceof \DateTimeInterface) return $v->format(DATE_ATOM);
        if (is_string($v) && $v !== '')       return (new \DateTimeImmutable($v))->format(DATE_ATOM);
        return null;
    }

    #[Route('', methods:['GET'])]
    public function inbox(Request $req, MessageRepository $repo): JsonResponse
    {
        $me = $this->getUser()->getUserIdentifier();
        $page  = max(1, (int)$req->query->get('page', 1));
        $limit = max(1, (int)$req->query->get('limit', 50));
        $unreadOnly = (bool)$req->query->get('unread', false);

        $rows = $repo->findInbox($me, $unreadOnly, $page, $limit);
        $unreadCount = $repo->countUnread($me);

        $data = array_map(fn($m)=>[
            'id'      => $m['id'],
            'from'    => $m['senderEmail'],
            'to'      => $m['receiverEmail'],
            'date'    => $this->fmtDate($m['createdAt']),
            'preview' => mb_strimwidth($m['content'], 0, 80, '…'),
            'is_read' => !empty($m['readAt']),
        ], $rows);

        return $this->json(['data'=>$data, 'unread'=>$unreadCount, 'page'=>$page, 'limit'=>$limit]);
    }

    #[Route('/conversations', methods:['GET'])]
    public function conversations(MessageRepository $repo): JsonResponse
    {
        $me = $this->getUser()->getUserIdentifier();
        $rows = $repo->findLastByCorrespondent($me);

        $data = array_map(function($m) use ($me) {
            $sender   = $m['sender_email']   ?? $m['senderEmail']   ?? null;
            $receiver = $m['receiver_email'] ?? $m['receiverEmail'] ?? null;
            $created  = $m['created_at']     ?? $m['createdAt']     ?? null;
            $readAt   = $m['read_at']        ?? $m['readAt']        ?? null;
            $other    = $sender === $me ? $receiver : $sender;

            $isRead = !($receiver === $me && empty($readAt));

            return [
                'id'           => $m['id'],
                'with'         => $other,
                'last_date'    => $this->fmtDate($created),
                'last_preview' => mb_strimwidth($m['content'], 0, 80, '…'),
                'is_read'      => $isRead,
            ];
        }, $rows);

        return $this->json(['data' => $data]);
    }

    #[Route('/with/{other}', methods:['GET'])]
    public function conversation(string $other, Request $req, MessageRepository $repo, EM $em): JsonResponse
    {
        $me    = $this->getUser()->getUserIdentifier();
        $page  = max(1, (int)$req->query->get('page', 1));
        $limit = max(1, (int)$req->query->get('limit', 200));

        $rows = $repo->findConversation($me, $other, $page, $limit);

        $data = array_map(fn($m)=>[
            'id'      => $m['id'],
            'from'    => $m['senderEmail'],
            'to'      => $m['receiverEmail'],
            'date'    => $this->fmtDate($m['createdAt']),
            'content' => $m['content'],
        ], $rows);

        $em->createQueryBuilder()
            ->update(Message::class, 'm')
            ->set('m.readAt', ':now')
            ->where('m.receiverEmail = :me')
            ->andWhere('m.senderEmail = :other')
            ->andWhere('m.readAt IS NULL')
            ->setParameter('me', $me)
            ->setParameter('other', $other)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();

        return $this->json(['data' => $data, 'with' => $other, 'page' => $page, 'limit' => $limit]);
    }


    #[Route('', methods:['POST'])]
    public function send(Request $req, EM $em): JsonResponse
    {
        $me = $this->getUser()->getUserIdentifier();
        $data = json_decode($req->getContent(), true) ?? [];
        $to = (string)($data['to'] ?? '');
        $content = (string)($data['content'] ?? '');

        if ($to === '' || $content === '') {
            return $this->json(['error'=>'to and content are required'], 422);
        }
        $msg = (new Message())
            ->setSenderEmail($me)
            ->setReceiverEmail($to)
            ->setContent($content);

        $em->persist($msg); $em->flush();

        return $this->json(['id'=>$msg->getId()], 201);
    }

    #[Route('/{id<\d+>}/read', methods:['POST'])]
    public function markRead(Message $message, EM $em): JsonResponse
    {
        $me = $this->getUser()->getUserIdentifier();
        if ($message->getReceiverEmail() !== $me) {
            return $this->json(['error'=>'forbidden'], 403);
        }
        if (!$message->isRead()) {
            $message->markRead();
            $em->flush();
        }
        return $this->json(['ok'=>true]);
    }

    #[Route('/unread-count', methods:['GET'])]
    public function unreadCount(MessageRepository $repo): JsonResponse
    {
        $me = $this->getUser()->getUserIdentifier();
        return $this->json(['unread' => $repo->countUnread($me)]);
    }
}
