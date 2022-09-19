<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Dump Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk where database dumps are hosted. By default, it is assumed
    | that database dumps are hosted in an S3-like bucket.
    |
    */

    'disk' => env('DUMP_DISK', 'dumps'),

    /*
    |--------------------------------------------------------------------------
    | Dump Domain
    |--------------------------------------------------------------------------
    |
    | These values represent the base URL that dumps are downloaded from.
    | It is most likely that only one of these values should be set.
    | If deumps are downloaded from a subdomain, set DUMP_URL and leave DUMP_PATH null.
    | Ex: dump.animethemes.test
    | If dumps are NOT streamed from a subdomain, set DUMP_PATH and leave DUMP_URL null.
    | Ex: animethemes.test/dump
    |
    */

    'url' => env('DUMP_URL'),

    'path' => env('DUMP_PATH'),
];
