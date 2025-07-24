<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Song\SongResourceTab;

class SongYoutubeMusicResourceTab extends SongResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'song-youtube-music-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE_MUSIC;
    }
}
