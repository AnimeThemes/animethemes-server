<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class AnimeAnilistResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'anime-anilist-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::ANILIST;
    }
}
