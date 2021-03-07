<?php

declare(strict_types=1);

return [
    'hosts' => [
        [
            'host' => env('ELASTIC_HOST'),
            'port' => env('ELASTIC_PORT'),
            'scheme' => env('ELASTIC_SCHEME'),
            'user' => env('ELASTIC_USER'),
            'pass' => env('ELASTIC_PASS'),
        ],
    ],
];
