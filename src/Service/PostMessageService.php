<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\DanceEvent;
use App\DTO\DanceEventCollection;
use App\Enum\Weekdays;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class PostMessageService
{
    public function __construct(
        private Telegram $telegram,
        private string $telegramChannel,
    ) {
    }

    private function handleExtras(array $extras, array &$out): void
    {
        foreach ($extras['responses'] ?? [] as $r) {
            $out[] = $r;
        }
    }

    public function handleEvents(DanceEventCollection $events): array
    {
        $dayOfTheWeek = (int) date('w');

        $extras = [
            'protect_content' => 'true',
            'disable_notification' => 'true',
        ];

        $out = [];

        // Handles no events returned from API and returns
        if ($events->size === 0) {
            $this->sendMessage(
                $this->getNoEventsTodayMessage(),
                $extras,
            );
            $this->handleExtras($extras, $out);
            return $out;
        }

        $this->sendMessage(
            $this->getIntroMessage($dayOfTheWeek),
            $extras,
        );
        $this->handleExtras($extras, $out);

        foreach ($events->danceEvents as $event) {
            $this->sendMessage(
                $this->createMessageFromDanceEvent($event, $dayOfTheWeek),
                $extras
            );
            $this->handleExtras($extras, $out);
        }

        return $out;
    }

    private function getIntroMessage(int $dayOfTheWeek): string
    {
        $msg = '🤖🤖🤖'.PHP_EOL;
        if (Weekdays::isFriday($dayOfTheWeek)) {
            $msg .= 'Hallo, das sind eure Social Dances am Wochenende.';
        } else {
            $msg .= 'Hallo, das ist euer tägliches Swing Dance Update.';
        }
        $msg .= PHP_EOL;
        $msg .= 'See you on the dancefloor! 🫶';

        return $msg;
    }

    private function getNoEventsTodayMessage(): string
    {
        $msg = '🤖🤖🤖'.PHP_EOL;
        $msg .= 'Leider weiss ich heute von keinen Socials.' . PHP_EOL;
        $msg .= 'Schaut doch einfach später auf https://rmswing.de vorbei.';

        return $msg;
    }

    private function sendMessage(string $message, array &$options = []): void
    {
        Request::sendMessage(
            array_merge(
                [
                'chat_id' => $this->telegramChannel,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                ],
            ),
            $options
        );
    }

    private function getTimeDate(DanceEvent $danceEvent, int $dayOfTheWeek): string
    {
        $parts = [];
        if (Weekdays::isFriday($dayOfTheWeek)) {
            $parts[] = sprintf('📅 %s', Weekdays::from((int) $danceEvent->startDateTime->format('w'))->name);
        }
        $parts[] = sprintf('⏰ %s Uhr', $danceEvent->startDateTime->format('H:i'));

        return implode(' ', $parts);
    }

    private function createMessageFromDanceEvent(DanceEvent $danceEvent, int $dayOfTheWeek): string
    {
        $parts = [];
        $parts[] = sprintf('⭐ <b>%s</b> in <b>%s</b> ⭐', $danceEvent->summary, $danceEvent->city);
        $parts[] = '';
        $parts[] = $this->getTimeDate($danceEvent, $dayOfTheWeek);
        $parts[] = sprintf('📍 %s', $danceEvent->location);
        $parts[] = '';
        $parts[] = sprintf('<a href="https://rmswing.de/geteilt?%s">🔗 Mehr Infos: Klick mich!</a>', $danceEvent->id);

        return implode(PHP_EOL, $parts);
    }
}
