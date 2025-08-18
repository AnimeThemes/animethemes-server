<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Base\ImageTab;

class AnimeLargeCoverTab extends ImageTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'anime-large-cover-tab';
    }

    /**
     * The image facet.
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::LARGE_COVER;
    }
}
