<?php
declare(strict_types=1);

namespace App\DTO;

use DateTime;
use DateTimeImmutable;

class DanceEvent {
    public function __construct(
        public readonly int $id,
        public readonly string $eventId,
        public readonly string $instanceId,
        public readonly string $summary,
        public readonly string $description,
        public readonly DateTimeImmutable $startDateTime,
        public readonly DateTimeImmutable $endDateTime,
        public readonly string $location,
        public readonly ?string $city
    ){}
}