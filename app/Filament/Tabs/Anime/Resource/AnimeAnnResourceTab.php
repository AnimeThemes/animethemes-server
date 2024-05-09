<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Anime\AnimeResourceTab;

/**
 * Class AnimeAnnResourceTab.
 */
class AnimeAnnResourceTab extends AnimeResourceTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'anime-ann-resource-tab';
    }

    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANN;
    }
}