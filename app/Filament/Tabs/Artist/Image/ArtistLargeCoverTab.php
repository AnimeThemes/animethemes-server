<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Artist\ArtistImageTab;

/**
 * Class ArtistLargeCoverTab.
 */
class ArtistLargeCoverTab extends ArtistImageTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'artist-large-cover-tab';
    }

    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::LARGE_COVER;
    }
}
