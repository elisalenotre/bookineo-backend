<?php
namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Message::class); }

    /** Inbox du user */
    public function findInbox(string $email, bool $unreadOnly, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.receiverEmail = :me')->setParameter('me', $email)
            ->orderBy('m.createdAt', 'DESC')
            ->setFirstResult(($page-1)*$limit)->setMaxResults($limit);

        if ($unreadOnly) $qb->andWhere('m.readAt IS NULL');
        return $qb->getQuery()->getArrayResult();
    }

    public function countUnread(string $email): int
    {
        return (int)$this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.receiverEmail = :me')->setParameter('me', $email)
            ->andWhere('m.readAt IS NULL')
            ->getQuery()->getSingleScalarResult();
    }

    /** Conversation bilatérale (A <-> B) */
    public function findConversation(string $me, string $other, int $page, int $limit): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(m.senderEmail = :me AND m.receiverEmail = :other) OR (m.senderEmail = :other AND m.receiverEmail = :me)')
            ->setParameter('me', $me)->setParameter('other', $other)
            ->orderBy('m.createdAt', 'ASC')
            ->setFirstResult(($page-1)*$limit)->setMaxResults($limit)
            ->getQuery()->getArrayResult();
    }

    /** Dernier message par correspondant (aperçu conversations) */
    public function findLastByCorrespondent(string $me): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = <<<SQL
        SELECT t.*
        FROM (
          SELECT
            m.*,
            CASE
              WHEN m.sender_email = :me THEN m.receiver_email
              ELSE m.sender_email
            END AS correspondent
          FROM messages m
          WHERE m.sender_email = :me OR m.receiver_email = :me
        ) t
        JOIN (
          SELECT
            CASE
              WHEN sender_email = :me THEN receiver_email
              ELSE sender_email
            END AS correspondent,
            MAX(created_at) AS last_date
          FROM messages
          WHERE sender_email = :me OR receiver_email = :me
          GROUP BY 1
        ) last
        ON t.correspondent = last.correspondent AND t.created_at = last.last_date
        ORDER BY t.created_at DESC
        SQL;

        return $conn->executeQuery($sql, ['me' => $me])->fetchAllAssociative();
    }
}
