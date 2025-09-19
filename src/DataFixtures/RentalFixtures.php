<?php

namespace App\DataFixtures;

use App\Entity\Rental;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RentalFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Book $book1 */
        $book1 = $this->getReference('book.demo.1', Book::class);
        /** @var Book $book2 */
        $book2 = $this->getReference('book.demo.2', Book::class);

        $r1 = new Rental();
        $r1->setBook($book1);
        $r1->setRenterFirstName('Alice');
        $r1->setRenterLastName('Martin');
        $r1->setStartDate(new \DateTime('now'));
        $due1 = (new \DateTime('now'))->modify('+14 days');
        $r1->setDueDate($due1);

        $book1->setStatus('rented');
        $manager->persist($r1);

        $r2 = new Rental();
        $r2->setBook($book2);
        $r2->setRenterFirstName('Bob');
        $r2->setRenterLastName('Durand');
        $r2->setStartDate(new \DateTime('-30 days'));
        $due2 = (new \DateTime('-30 days'))->modify('+10 days');
        $r2->setDueDate($due2);
        $r2->setReturnDate((clone $due2)->modify('+2 days'));
        $r2->setComment('Retour avec légère usure');

        $book2->setStatus('available');
        $manager->persist($r2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }
}
