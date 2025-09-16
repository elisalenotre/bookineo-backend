<?php 

namespace App\Domain;

interface UserRepositoryInterface
{
    public function getId(int $id): array;
    
    public function create(): void;
    public function delete(): void;
    public function save(): array;

}