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
    | To resolve the featured theme, we need to provide the desired pivot row between entry and video.
    | To resolve the desired pivot row, these values should correspond to the primary keys of the respective models.
    |
    */

    'featured_entry' => (int) env('WIKI_FEATURED_ENTRY'),

    'featured_video' => (int) env('WIKI_FEATURED_VIDEO'),
];
