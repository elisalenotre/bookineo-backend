<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/rentals')]
class RentalsController extends AbstractController
{
    #[Route('/_health', methods: ['GET'])]
    public function health()
    {
        return $this->json(['ok' => true]);
    }
}
