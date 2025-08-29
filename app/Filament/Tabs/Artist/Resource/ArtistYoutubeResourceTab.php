<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class ArtistYoutubeResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'artist-youtube-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE;
    }
}
