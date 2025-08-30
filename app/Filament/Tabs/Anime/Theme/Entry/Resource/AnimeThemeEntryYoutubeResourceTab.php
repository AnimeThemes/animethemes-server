<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Theme\Entry\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class AnimeThemeEntryYoutubeResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'entry-youtube-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE;
    }
}
