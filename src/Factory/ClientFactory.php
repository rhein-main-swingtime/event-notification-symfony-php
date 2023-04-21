<?php

namespace App\Factory;

use GuzzleHttp\Client;

class ClientFactory
{
    private const API_URL = 'https://api.rmswing.de/';

    private static function config()
    {
        return [
            // Base URI is used with relative requests
            'base_uri' => self::API_URL,
            // You can set any number of default request options.
            'timeout' => 2.0,
        ];
    }

    public static function create(): Client
    {
        return new Client(self::config());
    }
}
