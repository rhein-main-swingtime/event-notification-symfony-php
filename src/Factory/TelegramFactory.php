<?php

namespace App\Factory;

use Longman\TelegramBot\Telegram;

class TelegramFactory
{
    private const BOT_USERNAME = 'rmswing_bot';

    public static function create(string $token): Telegram
    {
        return new Telegram(
            $token,
            self::BOT_USERNAME
        );
    }
}
