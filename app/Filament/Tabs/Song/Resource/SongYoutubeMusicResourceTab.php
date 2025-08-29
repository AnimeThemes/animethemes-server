<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class SongYoutubeMusicResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'song-youtube-music-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE_MUSIC;
    }
}
