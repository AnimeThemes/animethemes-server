<?php

declare(strict_types=1);

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                [
                    'host' => env('ELASTIC_HOST', 'localhost'),
                    'port' => env('ELASTIC_PORT', '9200'),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                    'user' => env('ELASTIC_USER'),
                    'pass' => env('ELASTIC_PASS'),
                ],
            ],
        ],
    ],
];
