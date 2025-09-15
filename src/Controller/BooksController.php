<?php
namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/books')]
class BooksController extends AbstractController
{
    #[Route('', methods:['GET'])]
    public function list(Request $req, BookRepository $repo)
    {
        $q = $req->query;
        $qb = $repo->createQueryBuilder('b');

        if ($q->get('q'))       $qb->andWhere('b.title LIKE :q')->setParameter('q','%'.$q->get('q').'%');
        if ($q->get('author'))  $qb->andWhere('b.author LIKE :a')->setParameter('a','%'.$q->get('author').'%');
        if ($q->get('status'))  $qb->andWhere('b.status = :s')->setParameter('s',$q->get('status'));
        if ($q->get('price_min') !== null) $qb->andWhere('b.price >= :pmin')->setParameter('pmin',(float)$q->get('price_min'));
        if ($q->get('price_max') !== null) $qb->andWhere('b.price <= :pmax')->setParameter('pmax',(float)$q->get('price_max'));

        $page = max(1,(int)$q->get('page',1)); $limit = max(1,(int)$q->get('limit',10));
        $qb->setFirstResult(($page-1)*$limit)->setMaxResults($limit);

        $items = $qb->getQuery()->getArrayResult();
        return $this->json(['data'=>$items,'page'=>$page,'limit'=>$limit]);
    }

    #[Route('', methods:['POST'])]
    public function create(Request $req, EM $em)
    {
        $data = json_decode($req->getContent(), true) ?? [];
        $b = new Book();
        $b->setTitle($data['title'] ?? null);
        $b->setAuthor($data['author'] ?? null);
        if (!empty($data['publication_date'])) $b->setPublicationDate(new \DateTime($data['publication_date']));
        $b->setStatus($data['status'] ?? 'available');
        $b->setPrice((float)($data['price'] ?? 0));
        $b->setOwner($this->getUser()->getUserIdentifier()); // owner = email
        $b->setDescription($data['description'] ?? null);

        $em->persist($b); $em->flush();
        return $this->json(['id'=>$b->getId()], 201);
    }

    #[Route('/{id}', methods:['GET'])]
    public function detail(Book $book) { return $this->json($book); }

    #[Route('/{id}', methods:['PUT'])]
    public function update(Book $book, Request $req, EM $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($req->getContent(), true) ?? [];
        if (isset($data['title'])) $book->setTitle($data['title']);
        if (isset($data['author'])) $book->setAuthor($data['author']);
        if (isset($data['price'])) $book->setPrice((float)$data['price']);
        if (isset($data['status'])) $book->setStatus($data['status']);
        if (isset($data['description'])) $book->setDescription($data['description']);
        if (isset($data['publication_date'])) $book->setPublicationDate(new \DateTime($data['publication_date']));
        $em->flush();
        return $this->json(['message'=>'Livre mis à jour']);
    }

    #[Route('/{id}', methods:['DELETE'])]
    public function delete(Book $book, EM $em)
    {
        $em->remove($book); $em->flush();
        return $this->json(null, 204);
    }

    #[Route('/export', methods:['GET'])]
    public function exportCsv(Request $req, BookRepository $repo): StreamedResponse
    {
        $req->query->set('page', 1); $req->query->set('limit', 10000);
        $data = $this->list($req, $repo)->getContent();
        $rows = json_decode($data,true)['data'] ?? [];

        $response = new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','title','author','publication_date','status','price','owner','description']);
            foreach ($rows as $r) fputcsv($out, [
                $r['id'], $r['title'], $r['author'],
                $r['publicationDate'] ?? null, $r['status'], $r['price'], $r['owner'], $r['description'] ?? ''
            ]);
            fclose($out);
        });
        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition','attachment; filename="books.csv"');
        return $response;
    }
}
