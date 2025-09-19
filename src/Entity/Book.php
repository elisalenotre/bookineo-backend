<?php
namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(name: "books")]
class Book
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $title = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $author = null;

    #[ORM\Column(name:"publication_date", type:"date", nullable:true)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column(type:"string", length:255)]
    private string $status = 'available'; // 'available' | 'rented'

    #[ORM\Column(type:"float")]
    private float $price = 0;

    // owner = email du propriétaire
    #[ORM\Column(type:"string", length:255)]
    private string $owner;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $description = null;

    #[ORM\Column(length:100, nullable:true)]
    private ?string $genre = null;

    // qui a loué (email)
    #[ORM\Column(type: 'string', length: 320, nullable: true)]
    private ?string $renterEmail = null;

    // date de début de location
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $rentedAt = null;

    // date de retour
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $returnedAt = null;

    // commentaire de restitution (facultatif)
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastReturnComment = null;

    // --- getters / setters ---
    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $v): self { $this->title = $v; return $this; }

    public function getAuthor(): ?string { return $this->author; }
    public function setAuthor(?string $v): self { $this->author = $v; return $this; }

    public function getPublicationDate(): ?\DateTimeInterface { return $this->publicationDate; }
    public function setPublicationDate(?\DateTimeInterface $v): self { $this->publicationDate = $v; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $v): self { $this->status = $v; return $this; }

    public function getPrice(): float { return $this->price; }
    public function setPrice(float $v): self { $this->price = $v; return $this; }

    public function getOwner(): string { return $this->owner; }
    public function setOwner(string $v): self { $this->owner = $v; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): self { $this->description = $v; return $this; }

    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(?string $genre): self { $this->genre = $genre; return $this; }
    public function getRenterEmail(): ?string { return $this->renterEmail; }
    public function setRenterEmail(?string $renterEmail): self { $this->renterEmail = $renterEmail; return $this; }

    public function getRentedAt(): ?\DateTimeInterface { return $this->rentedAt; }
    public function setRentedAt(?\DateTimeInterface $rentedAt): self { $this->rentedAt = $rentedAt; return $this; }

    public function getReturnedAt(): ?\DateTimeInterface { return $this->returnedAt; }
    public function setReturnedAt(?\DateTimeInterface $returnedAt): self { $this->returnedAt = $returnedAt; return $this; }

    public function getLastReturnComment(): ?string { return $this->lastReturnComment; }
    public function setLastReturnComment(?string $lastReturnComment): self { $this->lastReturnComment = $lastReturnComment; return $this; }
}
