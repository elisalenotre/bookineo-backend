<?php 

namespace App\Application\Command;

use App\Domain\UserRepositoryInterface;

class GetUserHandler 
{
    public function __construct(private UserRepositoryInterface $userRepositoryInterface)
    {

    }

    public function handle(int $id): array 
    {
        return $this->userRepositoryInterface->getId($id);
    }
}