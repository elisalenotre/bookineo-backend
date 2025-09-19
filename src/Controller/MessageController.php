<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/messages')]
class MessageController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(Request $req, MessageRepository $repo)
    {
        $q = $req->query;
        $qb = $repo->createQueryBuilder('b');

        $messages = $repo->findBy([], ['createdAt' => 'ASC']);
        $data = array_map(fn(Message $m) => [
            'id'       => $m->getId(),
            'sender'   => $m->getSender(),
            'receiver' => $m->getReceiver(),
            'content'  => $m->getContent(),
            'date'     => $m->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $messages);

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $req, EM $em)
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (empty($data['sender']) || empty($data['receiver']) || empty($data['content'])) {
            return $this->json(['error' => 'Missing fields'], 400);
        }

        $msg = new Message();
        $msg->setSender($data['sender']);
        $msg->setReceiver($data['receiver']);
        $msg->setContent($data['content']);

        $em->persist($msg); $em->flush();

        return $this->json([
            'id'       => $msg->getId(),
            'sender'   => $msg->getSender(),
            'receiver' => $msg->getReceiver(),
            'content'  => $msg->getContent(),
            'date'     => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
        ], 201);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Message $message, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($message);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/delete-conversation/{username}', methods: ['DELETE'])]
    public function deleteConversation(string $username, MessageRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $messages = $repo->createQueryBuilder('m')
            ->where('m.sender = :user OR m.receiver = :user')
            ->setParameter('user', $username)
            ->getQuery()
            ->getResult();

        foreach ($messages as $msg) {
            $em->remove($msg);
        }
        $em->flush();

        return $this->json(['message' => 'Conversation supprimée']);
    }
}
