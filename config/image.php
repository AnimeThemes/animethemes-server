<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Image Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk where images are hosted. By default, it is assumed
    | that images are hosted in an S3-like bucket.
    |
    */

    'disk' => env('IMAGE_DISK', 'images'),
];
