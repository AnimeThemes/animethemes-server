<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Allow Video Streams
    |--------------------------------------------------------------------------
    |
    | When video streams are allowed, requests to the video.show route will
    | stream video. If disabled, requests to the video.show route will
    | raise a 403 Forbidden response.
    |
    */

    'allow_video_streams' => (bool) env('ALLOW_VIDEO_STREAMS', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Audio Streams
    |--------------------------------------------------------------------------
    |
    | When audio streams are allowed, requests to the audio.show route will
    | stream audio. If disabled, requests to the audio.show route will
    | raise a 403 Forbidden response.
    |
    */

    'allow_audio_streams' => (bool) env('ALLOW_AUDIO_STREAMS', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Discord Notifications
    |--------------------------------------------------------------------------
    |
    | When discord notifications are allowed, event listeners shall send discord
    | notifications to the configured discord channel through the configured bot.
    | If discord notifications are not allowed, event listeners shall not send
    | discord notifications.
    |
    */

    'allow_discord_notifications' => (bool) env('ALLOW_DISCORD_NOTIFICATIONS', false),

    /*
    |--------------------------------------------------------------------------
    | Allow View Recording
    |--------------------------------------------------------------------------
    |
    | When set to true, a view will be created for the viewable model in the show
    | action of the resource controller. When set to false, a view will not be
    | recorded.
    |
    */

    'allow_view_recording' => (bool) env('ALLOW_VIEW_RECORDING', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Dump Downloading
    |--------------------------------------------------------------------------
    |
    | When dump downloads are allowed, requests to the dump.show route will
    | download dumps. If disabled, requests to the dump.show route will
    | raise a 403 Forbidden response.
    |
    */

    'allow_dump_downloading' => (bool) env('ALLOW_DUMP_DOWNLOADING', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Script Downloading
    |--------------------------------------------------------------------------
    |
    | When script downloads are allowed, requests to the videoscript.show route will
    | download scripts. If disabled, requests to the videoscript.show route will
    | raise a 403 Forbidden response.
    |
    */

    'allow_script_downloading' => (bool) env('ALLOW_SCRIPT_DOWNLOADING', false),

    /*
    |--------------------------------------------------------------------------
    | Allow Playlist Management
    |--------------------------------------------------------------------------
    |
    | When playlist management is allowed, requests to write routes for playlist resources
    | permit authorized users. If disabled, requests to write routes for playlist resources
    | will raise a 403 Forbidden response.
    |
    */

    'allow_playlist_management' => (bool) env('ALLOW_PLAYLIST_MANAGEMENT', false),
];
