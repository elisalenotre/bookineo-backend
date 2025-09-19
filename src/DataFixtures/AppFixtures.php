<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('demo@bookineo.test');
        $user->setFirstName('Demo');
        $user->setLastName('User');
        $user->setPassword($this->hasher->hashPassword($user, 'DemoPwd!234'));
        $manager->persist($user);
        $this->addReference('user.demo', $user);

        $books = [
            ['The Picture Of Dorian Grey','Oscar Wilde','1890-07-01',5.0,'En très bon état'],
            ['Kitchen','Banana Yoshimoto','1988-01-01',4.0,'Usé mais correct'],
            ['Ms Ice Sandwich','Mieko Kawakami','2013-01-04',6.0,'Comme neuf'],
        ];

        $i = 1;
        foreach ($books as [$title,$author,$date,$price,$desc]) {
            $b = new Book();
            $b->setTitle($title);
            $b->setAuthor($author);
            $b->setPublicationDate(new \DateTime($date));
            $b->setPrice($price);
            $b->setStatus('available');
            $b->setOwner($user->getEmail());
            $b->setDescription($desc);
            $manager->persist($b);

            $this->addReference("book.demo.$i", $b);
            $i++;
        }

        $manager->flush();
    }
}
