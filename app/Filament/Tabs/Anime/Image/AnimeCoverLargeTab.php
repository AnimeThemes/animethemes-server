<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Anime\AnimeImageTab;

/**
 * Class AnimeCoverLargeTab.
 */
class AnimeCoverLargeTab extends AnimeImageTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'anime-cover-large-tab';
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