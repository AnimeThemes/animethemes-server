<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class ArtistAnidbResourceTab extends ResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'artist-anidb-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB;
    }
}
