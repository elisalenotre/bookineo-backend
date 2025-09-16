<?php
namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RentalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }

    // Exemples de helpers si tu veux
    // public function findActiveByBook(int $bookId): ?Rental
    // {
    //     return $this->createQueryBuilder('r')
    //         ->andWhere('r.book = :bid')
    //         ->andWhere('r.returnDate IS NULL')
    //         ->setParameter('bid', $bookId)
    //         ->getQuery()->getOneOrNullResult();
    // }
}
