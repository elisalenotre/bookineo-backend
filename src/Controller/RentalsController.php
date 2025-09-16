<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/rentals')]
class RentalsController extends AbstractController
{
    #[Route('', methods:['POST'])]
    public function rent(Request $req, EM $em)
    {
        $data = json_decode($req->getContent(), true) ?? [];
        /** @var Book $book */
        $book = $em->getRepository(Book::class)->find((int)$data['book_id']);
        if (!$book) return $this->json(['error'=>'Livre introuvable'],404);
        if ($book->getStatus() === 'rented') return $this->json(['error'=>'Déjà loué'],409);

        $r = new Rental();
        $r->setBook($book);
        $r->setRenterFirstName($data['renter_first_name']);
        $r->setRenterLastName($data['renter_last_name']);
        $r->setStartDate(new \DateTime($data['start_date']));
        $duration = (int)$data['duration_days'];
        $startDate = $r->getStartDate();
        $due = (new \DateTime($startDate->format('Y-m-d')))->modify("+$duration days");
        $r->setDueDate($due);

        $book->setStatus('rented');
        $em->persist($r); $em->flush();

        return $this->json(['id'=>$r->getId(),'due_date'=>$due->format('Y-m-d')],201);
    }

    #[Route('/{id}/return', methods:['POST'])]
    public function returnBook(int $id, Request $req, EM $em, RentalRepository $repo)
    {
        $r = $repo->find($id);
        if (!$r) return $this->json(['error'=>'Location introuvable'],404);
        $data = json_decode($req->getContent(), true) ?? [];
        $r->setReturnDate(new \DateTime($data['return_date']));
        $r->setComment($data['comment'] ?? null);

        $r->getBook()->setStatus('available');
        $em->flush();

        return $this->json(['message'=>'Livre restitué']);
    }

    #[Route('', methods:['GET'])]
    public function history(Request $req, RentalRepository $repo)
    {
        $q = $req->query; $qb = $repo->createQueryBuilder('r')->join('r.book','b');
        if ($q->get('book_id')) $qb->andWhere('b.id = :bid')->setParameter('bid',(int)$q->get('book_id'));
        if ($q->get('renter'))  $qb->andWhere('(r.renterFirstName LIKE :r OR r.renterLastName LIKE :r)')->setParameter('r','%'.$q->get('renter').'%');
        if ($q->get('from'))    $qb->andWhere('r.startDate >= :from')->setParameter('from', new \DateTime($q->get('from')));
        if ($q->get('to'))      $qb->andWhere('r.startDate <= :to')->setParameter('to', new \DateTime($q->get('to')));
        $rows = $qb->getQuery()->getArrayResult();
        return $this->json($rows);
    }
}

