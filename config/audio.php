<?php

declare(strict_types=1);

return [

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
];
