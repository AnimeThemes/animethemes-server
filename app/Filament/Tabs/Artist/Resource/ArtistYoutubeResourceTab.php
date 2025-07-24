<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Artist\ArtistResourceTab;

class ArtistYoutubeResourceTab extends ArtistResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'artist-youtube-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE;
    }
}
