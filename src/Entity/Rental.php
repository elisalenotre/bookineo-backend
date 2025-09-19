<?php
namespace App\Entity;

use App\Entity\Book;
use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: "rentals")]
class Rental
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(name: "book_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private Book $book;

    #[ORM\Column(name: "renter_first_name", type: "string", length: 255)]
    private string $renterFirstName;

    #[ORM\Column(name: "renter_last_name", type: "string", length: 255)]
    private string $renterLastName;

    #[ORM\Column(name: "start_date", type: "date")]
    private \DateTimeInterface $startDate;

    #[ORM\Column(name: "due_date", type: "date")]
    private \DateTimeInterface $dueDate;

    #[ORM\Column(name: "return_date", type: "date", nullable: true)]
    private ?\DateTimeInterface $returnDate = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 180, nullable: true)]
    private string $renterEmail;


    // --- getters / setters ---
    public function getId(): ?int { return $this->id; }

    public function getBook(): Book { return $this->book; }
    public function setBook(Book $book): self { $this->book = $book; return $this; }

    public function getRenterFirstName(): string { return $this->renterFirstName; }
    public function setRenterFirstName(string $v): self { $this->renterFirstName = $v; return $this; }

    public function getRenterLastName(): string { return $this->renterLastName; }
    public function setRenterLastName(string $v): self { $this->renterLastName = $v; return $this; }

    public function getStartDate(): \DateTimeInterface { return $this->startDate; }
    public function setStartDate(\DateTimeInterface $v): self { $this->startDate = $v; return $this; }

    public function getDueDate(): \DateTimeInterface { return $this->dueDate; }
    public function setDueDate(\DateTimeInterface $v): self { $this->dueDate = $v; return $this; }

    public function getReturnDate(): ?\DateTimeInterface { return $this->returnDate; }
    public function setReturnDate(?\DateTimeInterface $v): self { $this->returnDate = $v; return $this; }

    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $v): self { $this->comment = $v; return $this; }

   public function getRenterEmail(): ?string { return $this->renterEmail; }
    public function setRenterEmail(?string $renterEmail): self { $this->renterEmail = $renterEmail; return $this; }
}
