<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    |
    | These values represent caps on external profiles to prevent spam. By default,
    | an individual external profile is permitted 1000 entries, and a user
    | is permitted 5 external profiles.
    |
    */

    'profile_max_entries' => (int) env('PROFILE_MAX_ENTRIES', 1000),

    'user_max_profiles' => (int) env('USER_MAX_PROFILES', 5),
];
