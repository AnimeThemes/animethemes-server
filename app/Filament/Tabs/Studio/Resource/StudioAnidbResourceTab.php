<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class StudioAnidbResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'studio-anidb-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB;
    }
}
