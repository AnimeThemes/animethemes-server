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
    | redirect the user to the welcome page.
    |
    */

    'allow_video_streams' => (bool) env('ALLOW_VIDEO_STREAMS', false),

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
];
