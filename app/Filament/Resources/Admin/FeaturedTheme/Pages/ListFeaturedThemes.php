<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\FeaturedTheme\Pages;

use App\Filament\Resources\Admin\FeaturedThemeResource;
use App\Filament\Resources\Base\BaseListResources;

class ListFeaturedThemes extends BaseListResources
{
    protected static string $resource = FeaturedThemeResource::class;
}
