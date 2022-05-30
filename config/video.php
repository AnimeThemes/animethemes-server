<?php

declare(strict_types=1);

return [

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

    'url' => env('VIDEO_URL', env('APP_URL').'/video'),

    'path' => env('VIDEO_PATH'),
];
