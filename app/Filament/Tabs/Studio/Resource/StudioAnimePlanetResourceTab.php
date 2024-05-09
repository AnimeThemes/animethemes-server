<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Studio\StudioResourceTab;

/**
 * Class StudioAnimePlanetResourceTab.
 */
class StudioAnimePlanetResourceTab extends StudioResourceTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'studio-anime-planet-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIME_PLANET;
    }
}