<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'discord' => [
        'token' => env('DISCORD_BOT_API_TOKEN'),
        'db_updates_discord_channel' => env('DB_UPDATES_DISCORD_CHANNEL'),
        'admin_discord_channel' => env('ADMIN_DISCORD_CHANNEL'),
    ],

    'mal' => [
        'token' => env('MAL_BEARER_TOKEN'),
    ],

    'do' => [
        'token' => env('DO_BEARER_TOKEN'),
    ],
];
