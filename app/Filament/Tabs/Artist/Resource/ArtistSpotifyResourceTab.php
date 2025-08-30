<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class ArtistSpotifyResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'artist-spotify-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::SPOTIFY;
    }
}
