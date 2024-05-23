<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\Series;

/**
 * Class CreateSeries.
 */
class CreateSeries extends BaseCreateResource
{
    protected static string $resource = Series::class;
}
