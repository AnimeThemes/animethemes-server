<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Filament\Resources\Wiki\Artist;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArtists extends ListRecords
{
    protected static string $resource = Artist::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
