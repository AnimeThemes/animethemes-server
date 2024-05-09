<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Song\SongResourceTab;

/**
 * Class SongAmazonMusicResourceTab.
 */
class SongAmazonMusicResourceTab extends SongResourceTab
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
        return 'song-amazon-music-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::AMAZON_MUSIC;
    }
}
