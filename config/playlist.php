<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Limits
    |--------------------------------------------------------------------------
    |
    | These values represent caps on playlists to prevent spam. By default,
    | an individual playlist is permitted 1000 tracks, and a user
    | is permitted 1000 playlists.
    |
    */

    'playlist_max_tracks' => (int) env('PLAYLIST_MAX_TRACKS', 1000),

    'user_max_playlists' => (int) env('USER_MAX_PLAYLISTS', 1000),
];
