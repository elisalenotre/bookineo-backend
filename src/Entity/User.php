<?php
namespace App\Entity;

use App\Infrastructure\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(name:"first_name", type:"string", length:255, nullable:true)]
    private ?string $firstName = null;

    #[ORM\Column(name:"last_name", type:"string", length:255, nullable:true)]
    private ?string $lastName = null;

    #[ORM\Column(type:"string", length:255, unique:true)]
    private string $email;

    #[ORM\Column(type:"string", length:255)]
    private string $password;

    // --- colonnes d'audit présentes en base ---
    #[ORM\Column(name:"created_at", type:"datetime", nullable:true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name:"updated_at", type:"datetime", nullable:true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(name:"created_by", type:"string", length:100, nullable:true)]
    private ?string $createdBy = null;

    #[ORM\Column(name:"updated_by", type:"string", length:100, nullable:true)]
    private ?string $updatedBy = null;

    // --- security ---
    public function getId(): ?int { return $this->id; }
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return ['ROLE_USER']; }
    public function eraseCredentials(): void {}

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $p): self { $this->password=$p; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): self { $this->email=$e; return $this; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $v): self { $this->firstName=$v; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $v): self { $this->lastName=$v; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $v): self { $this->createdAt=$v; return $this; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeInterface $v): self { $this->updatedAt=$v; return $this; }
    public function getCreatedBy(): ?string { return $this->createdBy; }
    public function setCreatedBy(?string $v): self { $this->createdBy=$v; return $this; }
    public function getUpdatedBy(): ?string { return $this->updatedBy; }
    public function setUpdatedBy(?string $v): self { $this->updatedBy=$v; return $this; }
}
