<?php

namespace App\Service;

use App\DTO\DanceEventCollection;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class RetrieveEventsService
{
    public function __construct(private Client $client) {}

    private function transformToDTOs(string $fromApi): DanceEventCollection {
        $content = json_decode($fromApi);
        return DanceEventCollection::createFromPayload($content->danceEvents);
    }

    private function validateResponse(ResponseInterface $response): bool {
        if ($response->getStatusCode() != 200) {
            return false;
        }

        if ($response->getBody()->getSize() == 0) {
            return false;
        }

        return true;
    }

    public function retrieveByDate(
        string $start,
        string $end,
        array $categories = ['socials']
    ): DanceEventCollection {

        $response = $this->client->get(
            'events/v1/list',
            [
                'query' => [
                    'category[]' => 'socials',
                    "from" => $start,
                    "to" => $end,
                ]
            ]
        );

        if (!$this->validateResponse($response)) {
            return [];
        }

        return $this->transformToDTOs($response->getBody()->getContents());
    }

}
