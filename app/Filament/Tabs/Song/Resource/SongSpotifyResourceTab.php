<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class SongSpotifyResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'song-spotify-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::SPOTIFY;
    }
}
