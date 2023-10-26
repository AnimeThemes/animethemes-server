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
        'db_updates_discord_channel' => env('DB_UPDATES_DISCORD_CHANNEL'),
        'admin_discord_channel' => env('ADMIN_DISCORD_CHANNEL'),
        'submissions_discord_channel' => env('SUBMISSIONS_DISCORD_CHANNEL'),
        'submissions_forum_tags' => [
            'winter' => env('WINTER_DISCORD_FORUM_TAG'),
            'spring' => env('SPRING_DISCORD_FORUM_TAG'),
            'summer' => env('SUMMER_DISCORD_FORUM_TAG'),
            'fall' => env('FALL_DISCORD_FORUM_TAG')
        ]
    ],

    'mal' => [
        'client' => env('MAL_CLIENT_ID'),
    ],

    'do' => [
        'token' => env('DO_BEARER_TOKEN'),
    ],

    'openai' => [
        'token' => env('OPENAI_BEARER_TOKEN'),
    ],
];
