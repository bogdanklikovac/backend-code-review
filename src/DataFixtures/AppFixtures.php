<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Service\MessageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use function Psl\Iter\random;

class AppFixtures extends Fixture
{
    public function __construct(private MessageService $messageService) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        foreach (range(1, 10) as $i) {
            // Use MessageService to create a new message
            $message = $this->messageService->createMessage(
                $faker->sentence,
                random([Message::STATUS_SENT, Message::STATUS_READ])
            );

            $manager->persist($message);
        }

        $manager->flush();
    }
}
