<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Web Domain
    |--------------------------------------------------------------------------
    |
    | These values represent the base URL that web routes are hosted on.
    | It is most likely that only one of these values should be set.
    | If the web routes are hosted on a subdomain or at the root domain, set WEB_URL and leave WEB_PATH null.
    | Ex: app.animethemes.test or animethemes.test
    | If the web routes are NOT hosted on a subdomain or at the root domain, set WEB_PATH and leave WEB_URL null.
    | Ex: animethemes.test/app
    |
    */

    'url' => env('WEB_URL'),

    'path' => env('WEB_PATH'),
];
