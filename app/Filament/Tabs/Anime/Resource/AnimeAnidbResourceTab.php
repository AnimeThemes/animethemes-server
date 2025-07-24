<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Anime\AnimeResourceTab;

class AnimeAnidbResourceTab extends AnimeResourceTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'anime-anidb-resource-tab';
    }

    /**
     * The resource site.
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB;
    }
}
