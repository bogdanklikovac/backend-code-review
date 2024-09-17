<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\MessageDTO;
use App\Entity\Message;
use Symfony\Component\Uid\Uuid;

// REVIEW: Create MessageService class that handles the transformation of Message entities into DTOs format suitable for transport in an API response
class MessageService
{
    /**
     * Transforms Message entities into DTOs.
     *
     * @param Message[] $messages
     * @return array
     */
    public function transformMessages(array $messages): array
    {
        return array_map(static function (Message $message) {
            $messageDTO = new MessageDTO(
                $message->getUuid(),
                $message->getText(),
                $message->getStatus()
            );

            return $messageDTO->toArray();
        }, $messages);
    }

    /**
     * Creates a new Message entity.
     *
     * @param string $text
     * @param string $status
     * @return Message
     */
    public function createMessage(string $text, string $status = Message::STATUS_SENT): Message
    {
        $message = new Message();
        $message->setUuid(Uuid::v6()->toRfc4122());
        $message->setText($text);
        $message->setStatus($status);
        $message->setCreatedAt(new \DateTime());

        return $message;
    }
}
