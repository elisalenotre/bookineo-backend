<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function updateMe(Request $req, EM $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $data = json_decode($req->getContent(), true) ?? [];

        if (array_key_exists('first_name', $data)) $user->setFirstName($data['first_name'] ?? '');
        if (array_key_exists('last_name',  $data)) $user->setLastName($data['last_name'] ?? '');

        if (array_key_exists('birth_date', $data)) {
            $bd = $data['birth_date'];
            if ($bd === null || $bd === '') {
                $user->setBirthDate(null);
            } else {
                $d = \DateTimeImmutable::createFromFormat('Y-m-d', substr((string)$bd, 0, 10));
                if ($d === false) {
                    return $this->json(['error' => 'Format de date invalide (attendu YYYY-MM-DD)'], 400);
                }
                $user->setBirthDate($d);
            }
        }

        $em->flush();

        return $this->json([
            'first_name'  => $user->getFirstName(),
            'last_name'   => $user->getLastName(),
            'birth_date'  => $user->getBirthDate()?->format('Y-m-d'),
            'email'       => $user->getUserIdentifier(),
        ]);
    }
}
