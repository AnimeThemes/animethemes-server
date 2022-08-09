<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Audio Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk where audios are hosted. By default, it is assumed
    | that audios are hosted in an S3-like bucket.
    |
    */

    'disk' => env('AUDIO_DISK', 'audios'),

    /*
    |--------------------------------------------------------------------------
    | Audio Domain
    |--------------------------------------------------------------------------
    |
    | These values represent the base URL that audios are streamed from.
    | It is most likely that only one of these values should be set.
    | If audios are streamed from a subdomain, set AUDIO_URL and leave AUDIO_PATH null.
    | Ex: a.animethemes.test
    | If audios are NOT streamed from a subdomain, set AUDIO_PATH and leave AUDIO_URL null.
    | Ex: animethemes.test/audio
    |
    */

    'url' => env('AUDIO_URL'),

    'path' => env('AUDIO_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Audio Streaming
    |--------------------------------------------------------------------------
    |
    | These values represent the method by which audio is streamed.
    | The first supported method of streaming is through a streamed response ("response").
    | The second supported method of streaming is through a Nginx internal redirect ("nginx").
    | A Nginx internal redirect requires a URI to match the location block.
    |
    */

    'streaming_method' => env('AUDIO_STREAMING_METHOD', 'response'),

    'nginx_redirect' => env('AUDIO_NGINX_REDIRECT', '/audio_redirect/'),
];
