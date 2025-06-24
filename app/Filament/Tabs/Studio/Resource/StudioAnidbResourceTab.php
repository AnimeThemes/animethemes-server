<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Studio\StudioResourceTab;

/**
 * Class StudioAnidbResourceTab.
 */
class StudioAnidbResourceTab extends StudioResourceTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'studio-anidb-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB;
    }
}