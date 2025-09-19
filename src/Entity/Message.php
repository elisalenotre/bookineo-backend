<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: "sender_first_name", type: 'string', length: 255)]
    private string $sender;

    #[ORM\Column(name: "receiver_first_name", type: 'string', length: 255)]
    private string $receiver;

    #[ORM\Column(name: "message_content", type: 'text')]
    private string $content;

    #[ORM\Column(name: "sent_date", type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // -------- getters / setters ----------
    public function getId(): ?int { return $this->id; }

    public function getSender(): string { return $this->sender; }
    public function setSender(string $sender): self { $this->sender = $sender; return $this; }

    public function getReceiver(): string { return $this->receiver; }
    public function setReceiver(string $receiver): self { $this->receiver = $receiver; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
