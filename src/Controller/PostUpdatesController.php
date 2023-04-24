<?php

namespace App\Controller;

use App\Enum\Weekdays;
use App\Service\PostMessageService;
use App\Service\RetrieveEventsService;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
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
        private string $postUpdatesAuth,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/post-updates', name: 'post_updates')]
    public function index(Request $request): JsonResponse
    {
        $auth = $request->query->get('auth') ?? null;

        if (
            !$this->getParameter('kernel.debug')
            && $auth !== $this->postUpdatesAuth
        ) {
            $this->logger->info('Denied request due to wrong auth.');
            return new JsonResponse(
                ['status' => 'error'],
                500
            );
        }

        return $this->doUpdatePosting();
    }

    public function doUpdatePosting(): JsonResponse
    {
        $range = $this->generateDateRange((int) date('w'));
        $events = $this->retrieveEventsService->retrieveByDate(
            ...$range
        );
        $out = $this->postMessageService->handleEvents($events);
        $out['client_ip'] = $_SERVER['REMOTE_ADDR'] ?? null;

        return new JsonResponse($out);
    }

    public function generateDateRange(int $currentDate): array
    {
        $start = new \DateTimeImmutable();
        if (Weekdays::Freitag === Weekdays::from($currentDate)) {
            $end = new \DateTimeImmutable('+2 day');
        } else {
            $end = new \DateTimeImmutable('today');
        }

        return [
            $start->format(self::API_DATE_FORMAT),
            $end->format(self::API_DATE_FORMAT),
        ];
    }
}
