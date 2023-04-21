<?php

namespace App\Controller;

use App\Enum\Weekdays;
use App\Service\PostMessageService;
use App\Service\RetrieveEventsService;
use DateTimeImmutable;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostUpdatesController extends AbstractController
{
    private const API_DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private Client $client,
        private RetrieveEventsService $retrieveEventsService,
        private PostMessageService $postMessageService,
        private string $postUpdatesAuth
    ) {}

    #[Route('/post-updates', name: 'post_updates')]
    public function index(Request $request): JsonResponse
    {
        $auth = $request->query->get('auth') ?? null;
        if (
            $request !== null
            && !$this->getParameter('kernel.debug')
            && $auth !== $this->postUpdatesAuth
        ) {
            return new JsonResponse(
                ['status' => 'error'],
                500
            );
        }

        return $this->doUpdatePosting();
    }

    public function doUpdatePosting(): JsonResponse {
        $range = $this->generateDateRange((int) date('w'));
        $evens = $this->retrieveEventsService->retrieveByDate(
            ...$range
        );
        $out = $this->postMessageService->handleEvents($evens);
        $out['client_ip'] = $_SERVER['REMOTE_ADDR'];
        return new JsonResponse($out);
    }

    public function generateDateRange(int $currentDate): array {
        $start = new DateTimeImmutable();

        if (Weekdays::from($currentDate) === Weekdays::Freitag) {
            $end = new DateTimeImmutable('+2 day');
        } else {
            $end = new DateTimeImmutable('tomorrow');
        }

        return [
            $start->format(self::API_DATE_FORMAT),
            $end->format(self::API_DATE_FORMAT)
        ];
    }


}
