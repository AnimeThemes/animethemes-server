<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Artist\ArtistResourceTab;

/**
 * Class ArtistYoutubeResourceTab.
 */
class ArtistYoutubeResourceTab extends ArtistResourceTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'artist-youtube-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE;
    }
}