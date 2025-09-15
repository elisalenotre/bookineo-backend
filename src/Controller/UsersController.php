<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/users')]
class UsersController extends AbstractController
{
    #[Route('/me', methods:['GET'])]
    public function me(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $u = $this->getUser();
        if (!$u instanceof \App\Entity\User) {
            return $this->json(['error' => 'User entity not found'], 400);
        }
        return $this->json([
            'email' => $u->getUserIdentifier(),
            'first_name' => $u->getFirstName(),
            'last_name' => $u->getLastName()
        ]);
    }

    #[Route('/me', methods:['PUT'])]
    public function updateMe(Request $req, EM $em): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $u = $this->getUser();
        if (!$u instanceof \App\Entity\User) {
            return $this->json(['error' => 'User entity not found'], 400);
        }
        $data = json_decode($req->getContent(), true) ?? [];
        if (isset($data['first_name'])) $u->setFirstName($data['first_name']);
        if (isset($data['last_name']))  $u->setLastName($data['last_name']);
        $em->flush();
        return $this->json(['message'=>'Profil mis à jour']);
    }
}
