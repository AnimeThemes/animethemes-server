<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Filament\Resources\Wiki\Anime;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAnime extends ViewRecord
{
    protected static string $resource = Anime::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
