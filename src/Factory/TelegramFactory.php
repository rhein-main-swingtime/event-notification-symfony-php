<?php

namespace App\Factory;

use GuzzleHttp\Client;
use Longman\TelegramBot\Telegram;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class TelegramFactory  {

    private const BOT_USERNAME = 'rmswing_bot';

    public static function create(string $token): Telegram
    {
        return new Telegram(
            $token,
            self::BOT_USERNAME
        );
    }

}