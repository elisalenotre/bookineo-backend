<?php

namespace App\Application\Controller;

use App\Application\Command\GetUserHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetUserController extends AbstractController
{
    public function __construct(private GetUserHandler $userHandler){}

    #[Route('/me/{id}', methods:['GET'])]
    public function me(int $id): JsonResponse
    {
        $user = $this->userHandler->handle($id);
        // $u = $this->getUser();
        // if (!$u instanceof \App\Entity\User) {
        //     return $this->json(['error' => 'User entity not found'], 400);
        // }
        return $this->json($user);
        //     [
        //     'email' => $u->getUserIdentifier(),
        //     'first_name' => $u->getFirstName(),
        //     'last_name' => $u->getLastName()
        // ]
    }
}