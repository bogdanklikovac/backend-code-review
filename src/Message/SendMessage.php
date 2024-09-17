<?php
declare(strict_types=1);

namespace App\Message;

// REVIEW: The $text property should be private to enforce encapsulation. A getter method is included to allow controlled access to its value.
class SendMessage
{
    public function __construct(
        private string $text
    ) {}

    public function getText(): string
    {
        return $this->text;
    }
}

