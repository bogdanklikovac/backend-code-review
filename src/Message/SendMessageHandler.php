<?php
declare(strict_types=1);

namespace App\Message;

use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
/**
 * TODO: Cover with a test
 */
class SendMessageHandler
{
    public function __construct(
        private EntityManagerInterface $manager,
        private LoggerInterface $logger, // Added for logging
        private MessageService $messageService
    ) {}

    public function __invoke(SendMessage $sendMessage): void
    {
        try {
            // Use MessageService to create a new message
            $message = $this->messageService->createMessage($sendMessage->getText());

            $this->manager->persist($message);
            $this->manager->flush();
        } catch (\Throwable $e) {
            // Log the error and rethrow
            $this->logger->error('Error while sending message: ' . $e->getMessage());
            throw $e;
        }
    }
}