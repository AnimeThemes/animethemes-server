<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Studio\StudioResourceTab;

class StudioAnilistResourceTab extends StudioResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'studio-anilist-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANILIST;
    }
}
