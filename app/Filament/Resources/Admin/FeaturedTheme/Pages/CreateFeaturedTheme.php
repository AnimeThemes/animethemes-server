<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\FeaturedTheme\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Admin\FeaturedTheme;

/**
 * Class CreateFeaturedTheme.
 */
class CreateFeaturedTheme extends BaseCreateResource
{
    protected static string $resource = FeaturedTheme::class;
}
