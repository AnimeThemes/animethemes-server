<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Wiki\ExternalResource;

/**
 * Class CreateExternalResource.
 */
class CreateExternalResource extends BaseCreateResource
{
    protected static string $resource = ExternalResource::class;
}
