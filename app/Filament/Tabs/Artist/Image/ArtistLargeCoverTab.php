<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Artist\ArtistImageTab;

class ArtistLargeCoverTab extends ArtistImageTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'artist-large-cover-tab';
    }

    /**
     * The image facet.
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::LARGE_COVER;
    }
}
