<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\ExternalResourceResource;

class ListExternalResources extends BaseListResources
{
    protected static string $resource = ExternalResourceResource::class;

    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
