<?php

declare(strict_types=1);

use App\Enums\Http\StreamingMethod;

return [

    /*
    |--------------------------------------------------------------------------
    | Video Disks
    |--------------------------------------------------------------------------
    |
    | The filesystem disks where videos are hosted. By default, it is assumed
    | that the default video disk is an S3-like bucket.
    |
    */

    'default_disk' => env('VIDEO_DISK_DEFAULT', 'videos'),

    'disks' => explode(',', env('VIDEO_DISKS', [])),

    /*
    |--------------------------------------------------------------------------
    | Video Domain
    |--------------------------------------------------------------------------
    |
    | These values represent the base URL that videos are streamed from.
    | It is most likely that only one of these values should be set.
    | If videos are streamed from a subdomain, set VIDEO_URL and leave VIDEO_PATH null.
    | Ex: v.animethemes.test
    | If videos are NOT streamed from a subdomain, set VIDEO_PATH and leave VIDEO_URL null.
    | Ex: animethemes.test/video
    |
    */

    'url' => env('VIDEO_URL'),

    'path' => env('VIDEO_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Video Streaming
    |--------------------------------------------------------------------------
    |
    | These values represent the method by which video is streamed.
    | The first supported method of streaming is through a streamed response ("response").
    | The second supported method of streaming is through a Nginx internal redirect ("nginx").
    | A Nginx internal redirect requires a URI to match the location block.
    |
    */

    'streaming_method' => env('VIDEO_STREAMING_METHOD', StreamingMethod::RESPONSE),

    'nginx_redirect' => env('VIDEO_NGINX_REDIRECT', '/video_redirect/'),

    /*
    |--------------------------------------------------------------------------
    | Video Uploading
    |--------------------------------------------------------------------------
    |
    | These values facilitate the validation and uploading of video in-system to object storage.
    | For validation, we want to enforce that the latest FFmpeg version is used.
    | We will use the libavformat version of the form "Lavf{major}.{minor}.{patch]".
    |
    */

    'encoder_version' => env('VIDEO_ENCODER_VERSION'),

    /*
    |--------------------------------------------------------------------------
    | Video Rate Limiter
    |--------------------------------------------------------------------------
    |
    | This value represents the number of requests permitted to stream video per minute.
    | If set to a value less than or equal to zero, the limiter shall be unlimited.
    | If set to a value greater than 0, the limiter shall restrict by that value.
    |
    */

    'rate_limiter' => (int) env('VIDEO_RATE_LIMITER', -1),

    /*
    |--------------------------------------------------------------------------
    | Video Scripts
    |--------------------------------------------------------------------------
    |
    | Scripts represent the encoding script used to produce a video.
    | Generally, a script file will be a list of FFmpeg commands
    | to be executed in a target runtime environment.
    |
    */

    'script' => [

        /*
        |--------------------------------------------------------------------------
        | Script Disk
        |--------------------------------------------------------------------------
        |
        | The filesystem disk where video scripts are hosted. By default, it is assumed
        | that video scripts are hosted in an S3-like bucket.
        |
        */

        'disk' => env('SCRIPT_DISK', 'scripts'),

        /*
        |--------------------------------------------------------------------------
        | Script Domain
        |--------------------------------------------------------------------------
        |
        | These values represent the base URL that video scripts are downloaded from.
        | It is most likely that only one of these values should be set.
        | If scripts are downloaded from a subdomain, set SCRIPT_URL and leave SCRIPT_PATH null.
        | Ex: script.animethemes.test
        | If scripts are NOT downloaded from a subdomain, set SCRIPT_PATH and leave SCRIPT_URL null.
        | Ex: animethemes.test/videoscript
        |
        */

        'url' => env('SCRIPT_URL'),

        'path' => env('SCRIPT_PATH'),
    ],
];
