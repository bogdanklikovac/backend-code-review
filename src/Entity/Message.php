<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
/**
 * TODO: Review Message class
 */
class Message
{
    public const STATUS_SENT = 'sent';
    public const STATUS_READ = 'read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID)]
    private string $uuid; // UUID should not be nullable

    #[ORM\Column(length: 255)]
    private string $text; // REVIEW: The text field should not be nullable to ensure every message has valid content, simplifying validation and processing.

    #[ORM\Column(length: 255)]
    private string $status; // REVIEW: The status field must not be nullable. A null status clearly indicates whether a message was successfully sent or is still pending, preventing issues in reporting and ensuring a positive user experience.


    #[ORM\Column(type: 'datetime')]// REVIEW: createdAt should not be nullable
    private DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    #Validation for text: Protects against invalid or empty values for text.
    public function setText(string $text): self
    {
        if (empty($text)) {
            throw new \InvalidArgumentException('Text cannot be empty');
        }
        $this->text = $text;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    #Validation status: Protects against invalid or empty values for status values.
    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_SENT, self::STATUS_READ], true)) {
            throw new \InvalidArgumentException('Invalid status value');
        }
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }
}
