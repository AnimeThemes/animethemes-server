<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Anime\AnimeImageTab;

class AnimeSmallCoverTab extends AnimeImageTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'anime-small-cover-tab';
    }

    /**
     * The image facet.
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::SMALL_COVER;
    }
}
