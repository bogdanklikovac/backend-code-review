<?php
declare(strict_types=1);

namespace App\Tests\Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SendMessageHandlerTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MessageService $messageService;
    private SendMessageHandler $handler;

    protected function setUp(): void
    {
        // REVIEW: Create mock dependencies for EntityManager, Logger and MessageService
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->messageService = $this->createMock(MessageService::class);

        // REVIEW: Initialize SendMessageHandler with the mock objects
        $this->handler = new SendMessageHandler($this->entityManager, $this->logger, $this->messageService);
    }

    // REVIEW: Test that a message entity is persisted and flushed correctly
    public function test_it_persists_a_message(): void
    {
        // Mock the creation of a Message entity in MessageService
        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with('Test message')
            ->willReturn(new Message());

        // Expect EntityManager's persist method to be called once with a Message entity
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Message::class));

        // Expect EntityManager's flush method to be called once
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Create a SendMessage object and invoke the handler
        $message = new SendMessage('Test message');
        $this->handler->__invoke($message);
    }

    // REVIEW: Test that an error is logged when an exception occurs during message handling
    public function test_it_logs_on_error(): void
    {
        // Mock the creation of a Message entity in MessageService
        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with('Test message')
            ->willReturn(new Message());

        // Simulate an exception when EntityManager's flush method is called
        $this->entityManager
            ->method('flush')
            ->willThrowException(new \Exception('DB error'));

        // REVIEW: Expect Logger's error method to be called once with a relevant error message
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error while sending message'));

        // REVIEW: Create a SendMessage object to be passed to the handler
        $message = new SendMessage('Test message');
        $this->expectException(\Exception::class);

        $this->handler->__invoke($message);
    }
}
