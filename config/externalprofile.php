<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    |
    | These values represent caps on external profiles to prevent spam. By default,
    | a user is permitted 5 external profiles.
    */

    'user_max_profiles' => (int) env('USER_MAX_PROFILES', 5),
];
