<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    private MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    // REVIEW: The controller should delegate most of the business logic to services (Service Dependencies) rather than handling everything directly.
    // This keeps the controller thin and makes it easier to test and maintain.
    #[Route('/messages', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messageRepository): Response
    {
        $status = $request->query->get('status');

        // REVIEW: Passing the entire request object to a repository method is generally not a good practice.
        // It’s better to extract the needed parameters from the request. Use more descriptive method names in repositories.
        // Rename `by()` to `findByStatus()` to clarify that messages are being retrieved based on the 'status' parameter.
        $messages = $messageRepository->findByStatus($status);

        // REVIEW: Use the service to transform message entities
        $formattedMessages = $this->messageService->transformMessages($messages);

        // REVIEW: json_encode() function can be replaced with Symfony’s built-in JsonResponse class, which is more elegant and handles content-type headers automatically.
        return $this->json([
            'messages' => $formattedMessages,
        ]);

    }

    // REVIEW: POST should be used for actions that modify server state, improves security, and avoids caching issues.
    // Sending a message changes the state of the server, POST is the better choice.
    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $text = $request->request->get('text'); // Using POST data

        if (empty($text)) {
            // REVIEW: Returning an error message as a JSON response, maintaining consistency in API responses.
            return new JsonResponse(['error' => 'Text parameter is required.'], Response::HTTP_BAD_REQUEST);
        }

        $bus->dispatch(new SendMessage($text));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}