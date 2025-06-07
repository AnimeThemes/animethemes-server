<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Studio\StudioImageTab;

/**
 * Class StudioSmallCoverTab.
 */
class StudioSmallCoverTab extends StudioImageTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'studio-small-cover-tab';
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
