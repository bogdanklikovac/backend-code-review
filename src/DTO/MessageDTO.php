<?php
declare(strict_types=1);

namespace App\DTO;

// REVIEW:  Create MessageDTO as a Data Transfer Object (DTO) that holds message-related data.
// It encapsulates message information, providing a structure for transporting data between service layer and the controller.
class MessageDTO
{
    private string $uuid;
    private string $text;
    private string $status;

    public function __construct(string $uuid, string $text, string $status)
    {
        $this->uuid = $uuid;
        $this->text = $text;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'text' => $this->text,
            'status' => $this->status,
        ];
    }
}