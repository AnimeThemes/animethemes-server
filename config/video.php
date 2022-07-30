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
];
