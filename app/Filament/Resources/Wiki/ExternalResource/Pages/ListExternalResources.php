<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\Pages;

use App\Filament\Resources\Wiki\ExternalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExternalResources extends ListRecords
{
    protected static string $resource = ExternalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
