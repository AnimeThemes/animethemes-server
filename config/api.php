<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API Domain
    |--------------------------------------------------------------------------
    |
    | These values represent the base URL that the API is hosted on.
    | It is most likely that only one of these values should be set.
    | If the API is hosted on a subdomain, set API_URL and leave API_PATH null.
    | Ex: api.animethemes.test
    | If the API is NOT hosted on a subdomain, set API_PATH and leave API_URL null.
    | Ex: animethemes.test/api
    |
    */

    'url' => env('API_URL'),

    'path' => env('API_PATH'),
];
