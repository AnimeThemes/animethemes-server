<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Studio\StudioImageTab;

class StudioSmallCoverTab extends StudioImageTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'studio-small-cover-tab';
    }

    /**
     * The image facet.
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::SMALL_COVER;
    }
}
