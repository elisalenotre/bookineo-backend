<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name:'auth_register', methods:['POST'])]
    public function register(Request $req, EM $em, Hasher $hasher, UserRepository $repo): JsonResponse
    {
        $data = json_decode($req->getContent(), true) ?? [];
        $email = trim((string)($data['email'] ?? ''));
        $pwd   = (string)($data['password'] ?? '');
        $first = $data['first_name'] ?? null;
        $last  = $data['last_name'] ?? null;

        // règles sécurité simples (cf. cahier des charges)
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error'=>'Email invalide'], 400);
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}$/', $pwd)) {
            return $this->json(['error'=>'Mot de passe trop faible'], 400);
        }
        if ($repo->findOneBy(['email'=>$email])) {
            return $this->json(['error'=>'Email déjà utilisé'], 409);
        }

        $u = (new User())->setEmail($email)->setFirstName($first)->setLastName($last);
        $u->setPassword($hasher->hashPassword($u, $pwd));
        $em->persist($u); $em->flush();

        return $this->json(['message'=>'Compte créé'], 201);
    }

    #[Route('/login', name:'auth_login', methods:['POST'])]
    public function loginManual(
        Request $req,
        UserRepository $repo,
        Hasher $hasher,
        JWTTokenManagerInterface $jwt
    ): JsonResponse {
        $data = json_decode($req->getContent(), true) ?? [];
        $email = (string)($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');

        $user = $repo->findOneBy(['email' => $email]);
        if (!$user || !$hasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Identifiants invalides'], 401);
        }
        return $this->json(['token' => $jwt->create($user)]);
    }

}
