<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Feature\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Admin\Feature;

/**
 * Class CreateFeature.
 */
class CreateFeature extends BaseCreateResource
{
    protected static string $resource = Feature::class;
}
