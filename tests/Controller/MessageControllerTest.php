<?php
declare(strict_types=1);

namespace Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Symfony\Component\HttpFoundation\Response;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function test_list(): void
    {
        // REVIEW: Changed the HTTP method to GET for retrieving messages and passed the status as a query parameter.
        $client = static::createClient();
        $client->request('GET', '/messages', ['status' => 'sent']);

        // REVIEW: Added assertions to check the response's Content-Type and verify the response is valid JSON.
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertArrayHasKey('messages', $data);
        $this->assertIsArray($data['messages']);

        // REVIEW: Included checks for 'uuid', 'text', and 'status' keys in the first message if available.
        if (!empty($data['messages'])) {
            $message = $data['messages'][0];
            $this->assertArrayHasKey('uuid', $message);
            $this->assertArrayHasKey('text', $message);
            $this->assertArrayHasKey('status', $message);
        }
    }

    public function test_that_it_sends_a_message(): void
    {
        // REVIEW: Updated to use POST for sending messages and check for a no content response.
        $client = static::createClient();
        $client->request('POST', '/messages/send', ['text' => 'Hello World']);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // Ensure message was dispatched to the transport
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }

    public function test_send_message_without_text_should_fail(): void
    {
        // REVIEW: Added a test to validate that a missing 'text' parameter results in a 400 Bad Request response.
        $client = static::createClient();
        $client->request('POST', '/messages/send', []);

        // Assert bad request when 'text' is missing
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = $client->getResponse()->getContent();
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Text parameter is required.', $data['error']);
    }
}
