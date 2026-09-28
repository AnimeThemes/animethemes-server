<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Filament\Tabs\Base\ImageTab;

class AnimeImageTab extends ImageTab
{
    public static function getSlug(): string
    {
        return 'anime-image-tab';
    }
}
