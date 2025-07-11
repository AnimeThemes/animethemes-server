<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Artist\ArtistResourceTab;

/**
 * Class ArtistAnnResourceTab.
 */
class ArtistAnnResourceTab extends ArtistResourceTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'artist-ann-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANN;
    }
}
