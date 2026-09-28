<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio;

use App\Filament\Tabs\Base\ImageTab;

class StudioImageTab extends ImageTab
{
    public static function getSlug(): string
    {
        return 'studio-image-tab';
    }
}
