<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\FeaturedTheme\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Admin\FeaturedTheme;

/**
 * Class ListFeaturedThemes.
 */
class ListFeaturedThemes extends BaseListResources
{
    protected static string $resource = FeaturedTheme::class;
}
