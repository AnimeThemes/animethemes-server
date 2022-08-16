<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Video Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk where videos are hosted. By default, it is assumed
    | that videos are hosted in an S3-like bucket.
    |
    */

    'disk' => env('VIDEO_DISK', 'videos'),

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

    'streaming_method' => env('VIDEO_STREAMING_METHOD', 'response'),

    'nginx_redirect' => env('VIDEO_NGINX_REDIRECT', '/video_redirect/'),

    /*
    |--------------------------------------------------------------------------
    | Video Uploading
    |--------------------------------------------------------------------------
    |
    | These values facilitate the validation and uploading of video in-system to object storage.
    | For validation, we want to enforce that the latest FFmpeg version is used.
    | We will use the libavformat version of the form "Lavf{major}.{minor}.{patch]".
    | For uploading, we want to specify the target disks to upload the file.
    |
    */

    'encoder_version' => env('VIDEO_ENCODER_VERSION'),

    'upload_disks' => explode(',', env('VIDEO_UPLOAD_DISKS')),
];
