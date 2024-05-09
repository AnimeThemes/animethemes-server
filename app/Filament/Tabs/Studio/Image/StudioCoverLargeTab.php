<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Tabs\Studio\StudioImageTab;

/**
 * Class StudioCoverLargeTab.
 */
class StudioCoverLargeTab extends StudioImageTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'studio-cover-large-tab';
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
