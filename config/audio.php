<?php

declare(strict_types=1);

use App\Enums\Http\StreamingMethod;

return [

    /*
    |--------------------------------------------------------------------------
    | Audio Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disks where audios are hosted. By default, it is assumed
    | that the default audio disk is an S3-like bucket.
    |
    */

    'default_disk' => env('AUDIO_DISK_DEFAULT', 'audios'),

    'disks' => explode(',', env('AUDIO_DISKS', [])),

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

    'streaming_method' => env('AUDIO_STREAMING_METHOD', StreamingMethod::RESPONSE),

    'nginx_redirect' => env('AUDIO_NGINX_REDIRECT', '/audio_redirect/'),
];
