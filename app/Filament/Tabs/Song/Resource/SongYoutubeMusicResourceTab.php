<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Song\SongResourceTab;

/**
 * Class SongYoutubeMusicResourceTab.
 */
class SongYoutubeMusicResourceTab extends SongResourceTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getKey(): string
    {
        return 'song-youtube-music-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE_MUSIC;
    }
}
