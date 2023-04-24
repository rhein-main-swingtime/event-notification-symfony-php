<?php

namespace App\Service;

use App\DTO\DanceEventCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\Logger;

class RetrieveEventsService
{
    public function __construct(
        private Client $client,
        private LoggerInterface $loggerInterface
    ) {}

    private function transformToDTOs(string $fromApi): DanceEventCollection
    {
        $content = json_decode($fromApi);

        return DanceEventCollection::createFromPayload($content->danceEvents ?? []);
    }

    private function validateResponse(ResponseInterface $response): bool
    {
        if (200 != $response->getStatusCode()) {
            return false;
        }

        if (0 == $response->getBody()->getSize()) {
            return false;
        }

        return true;
    }

    public function retrieveByDate(
        string $start,
        string $end,
        array $categories = ['socials']
    ): DanceEventCollection {

        $retries = 0;
        while ($retries < 5) {
            try {
                $response = $this->client->get(
                    'events/v1/list',
                    [
                        'query' => [
                            'category[]' => 'socials',
                            'from' => $start,
                            'to' => $end,
                        ],
                    ]
                );
            } catch (GuzzleException $e) {
                $this->loggerInterface->error(
                    $e,
                    [
                        'attempt' => $retries
                    ]
                );
            }

            if ($response->getStatusCode() === 200) {
                break;
            }
            $retries++;

        }

        if (!$this->validateResponse($response)) {
            return [];
        }

        return $this->transformToDTOs($response->getBody()->getContents());
    }
}
