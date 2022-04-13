<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Featured Theme
    |--------------------------------------------------------------------------
    |
    | On the home page of the wiki client, there is a component for a featured theme.
    | The featured theme will showcase an OP/ED picked by staff or patrons.
    | The video for the featured theme will play muted, blurred and looped in the background of the page.
    | This value should be configured to use the basename of the video. Example: "Bakemonogatari-OP1.webm".
    |
    */

    'featured_theme' => env('WIKI_FEATURED_THEME'),

    /*
    |--------------------------------------------------------------------------
    | Client Account
    |--------------------------------------------------------------------------
    |
    | This is the user that is created to authenticate the client to the app.
    | This user will bypass our API rate limiter to support reasonable build times.
    |
    */

    'client_account' => env('WIKI_CLIENT_ACCOUNT'),
];
