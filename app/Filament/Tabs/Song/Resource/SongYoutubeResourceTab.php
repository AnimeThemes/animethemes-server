<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class SongYoutubeResourceTab extends ResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'song-youtube-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE;
    }
}
