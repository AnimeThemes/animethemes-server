<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Performance\Pages;

use App\Filament\Actions\Base\CreateAction;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\PerformanceResource;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance;
use Illuminate\Support\Arr;

class ListPerformances extends BaseListResources
{
    protected static string $resource = PerformanceResource::class;

    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->action(function (array $data): void {
                    $performances = Arr::get($data, Song::RELATION_PERFORMANCES);
                    PerformanceSongRelationManager::saveArtists(Arr::get($data, Performance::ATTRIBUTE_SONG), $performances);
                }),
        ];
    }
}
