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
        'scheme' => 'https',
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
        'api_url' => env('DISCORD_BOT_API_URL'),
        'api_key' => env('DISCORD_BOT_API_KEY'),
        'db_updates_discord_channel' => env('DB_UPDATES_DISCORD_CHANNEL'),
        'admin_discord_channel' => env('ADMIN_DISCORD_CHANNEL'),
    ],

    'openai' => [
        'token' => env('OPENAI_BEARER_TOKEN'),
    ],

    'anilist' => [
        'client_id' => env('ANILIST_CLIENT_ID'),
        'client_secret' => env('ANILIST_CLIENT_SECRET'),
        'redirect_uri' => env('ANILIST_REDIRECT_URI'),
    ],

    'mal' => [
        'client_id' => env('MAL_CLIENT_ID'),
        'client_secret' => env('MAL_CLIENT_SECRET'),
        'redirect_uri' => env('MAL_REDIRECT_URI'),
    ],
];
