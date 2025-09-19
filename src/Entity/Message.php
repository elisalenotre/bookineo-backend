<?php
namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: "messages")]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 320)]
    private string $senderEmail;

    #[Assert\NotBlank]
    #[ORM\Column(length: 320)]
    private string $receiverEmail;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // getters/setters
    public function getId(): ?int { return $this->id; }

    public function getSenderEmail(): string { return $this->senderEmail; }
    public function setSenderEmail(string $v): self { $this->senderEmail = $v; return $this; }

    public function getReceiverEmail(): string { return $this->receiverEmail; }
    public function setReceiverEmail(string $v): self { $this->receiverEmail = $v; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $v): self { $this->content = $v; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getReadAt(): ?\DateTimeImmutable { return $this->readAt; }
    public function markRead(): self { $this->readAt = new \DateTimeImmutable(); return $this; }

    public function isRead(): bool { return $this->readAt !== null; }
}
