<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Artist\ArtistImageTab;

/**
 * Class ArtistSmallCoverTab.
 */
class ArtistSmallCoverTab extends ArtistImageTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'artist-small-cover-tab';
    }

    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::SMALL_COVER;
    }
}
