<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Studio\StudioResourceTab;

class StudioAnimePlanetResourceTab extends StudioResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'studio-anime-planet-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIME_PLANET;
    }
}
