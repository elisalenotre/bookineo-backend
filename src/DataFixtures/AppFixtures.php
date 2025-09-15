<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // un user pour une demo potentielle
        $user = new User();
        $user->setEmail('demo2@bookineo.test');
        $user->setFirstName('Demo2');
        $user->setLastName('User2');
        $user->setPassword($this->hasher->hashPassword($user, 'Demo2Pwd!234'));
        $manager->persist($user);

        // quelques livres exemples
        $books = [
            ['The Picture Of Dorian Grey','Oscar Wilde','1890-07-01',5.0,'En très bon état'],
            ['Kitchen','Banana Yoshimoto','1988-01-01',4.0,'Usé mais correct'],
            ['Ms Ice Sandwich','Mieko Kawakami','2013-01-04',2.0,'Comme neuf'],
        ];

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
        }

        $manager->flush();
    }
}

