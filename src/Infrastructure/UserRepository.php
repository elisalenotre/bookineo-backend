<?php

namespace App\Infrastructure;

use App\Domain\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    public function getId(int $id): array{
        $SQL = "SELECT * FROM users WHERE id=:id";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($SQL);
        $statement->bindValue('id', $id);
        

        $result = $statement->executeQuery();
        return $result->fetchAllAssociative();
    }
    public function create(): void{
        $SQL = "";
    }
    public function delete(): void{
         $SQL = "";
    }
    public function save(): array{
         $SQL = "";
    }
}
