<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Artist\ArtistImageTab;

/**
 * Class ArtistCoverLargeTab.
 */
class ArtistCoverLargeTab extends ArtistImageTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'artist-cover-large-tab';
    }

    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::COVER_LARGE;
    }
}
