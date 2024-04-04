<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Filament\Resources\Wiki\Anime;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnime extends EditRecord
{
    protected static string $resource = Anime::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
