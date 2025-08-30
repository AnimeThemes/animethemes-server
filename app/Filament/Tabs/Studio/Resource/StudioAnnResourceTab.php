<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\Base\ResourceTab;

class StudioAnnResourceTab extends ResourceTab
{
    public static function getSlug(): string
    {
        return 'studio-ann-resource-tab';
    }

    protected static function site(): ResourceSite
    {
        return ResourceSite::ANN;
    }
}
