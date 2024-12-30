<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    |
    | These values represent caps on reports to prevent spam. By default,
    | a user is permitted 50 reports.
    */

    'user_max_reports' => (int) env('USER_MAX_REPORTS', 50),
];
