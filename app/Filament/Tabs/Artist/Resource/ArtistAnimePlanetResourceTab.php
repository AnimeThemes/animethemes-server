<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Artist\ArtistResourceTab;

/**
 * Class ArtistAnimePlanetResourceTab.
 */
class ArtistAnimePlanetResourceTab extends ArtistResourceTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'artist-anime-planet-resource-tab';
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
