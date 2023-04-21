<?php

namespace App\Controller;

use App\Enum\Weekdays;
use App\Service\PostMessageService;
use App\Service\RetrieveEventsService;
use DateTimeImmutable;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostUpdatesController extends AbstractController
{
    private const API_DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private Client $client,
        private RetrieveEventsService $retrieveEventsService,
        private PostMessageService $postMessageService
    ) {}

    #[Route('/post-updates', name: 'post_updates')]
    public function index(): JsonResponse
    {
        return $this->doUpdatePosting();
    }

    public function doUpdatePosting(): JsonResponse {
        $range = $this->generateDateRange((int) date('w'));
        $evens = $this->retrieveEventsService->retrieveByDate(
            ...$range
        );
        $out = $this->postMessageService->handleEvents($evens);
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
