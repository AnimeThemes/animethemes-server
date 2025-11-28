<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    |
    | These values represent caps on submissions to prevent spam. By default,
    | a user is permitted 50 submissions.
    */

    'user_max_submissions' => (int) env('USER_MAX_SUBMISSIONS', 50),
];
