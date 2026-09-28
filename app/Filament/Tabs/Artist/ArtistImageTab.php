<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist;

use App\Filament\Tabs\Base\ImageTab;

class ArtistImageTab extends ImageTab
{
    public static function getSlug(): string
    {
        return 'artist-image-tab';
    }
}
