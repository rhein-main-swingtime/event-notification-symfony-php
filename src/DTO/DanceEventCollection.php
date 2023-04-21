<?php

declare(strict_types=1);

namespace App\DTO;

/**
 *@property DanceEvent[] $danceEvents
 */
class DanceEventCollection
{
    public int $size = 0;

    private const DATE_TIME_FORMAT_JS = 'Y-m-d\TH:i:sP';

    private function __construct(public readonly array $danceEvents)
    {
        $this->size = count($this->danceEvents);
    }

    private static function createDateFromString(string $string): \DateTimeInterface
    {
        return \DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT_JS, $string);
    }

    private static function createEventFromPayload(\stdClass $payload)
    {
        return new DanceEvent(
            id: (int) $payload->id,
            eventId: $payload->event_id,
            instanceId: $payload->instance_id,
            summary: $payload->summary,
            description: $payload->description,
            startDateTime: self::createDateFromString($payload->start_date_time),
            endDateTime: self::createDateFromString($payload->end_date_time),
            city: $payload->city,
            location: $payload->location
        );
    }

    public static function createFromPayload(array $rawEvents): self
    {
        $events = array_map(
            fn ($e) => self::createEventFromPayload($e),
            $rawEvents
        );

        return new self($events);
    }
}
