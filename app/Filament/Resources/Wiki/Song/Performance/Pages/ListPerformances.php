<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Performance\Pages;

use App\Filament\Actions\Base\CreateAction;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\Performance;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Illuminate\Support\Arr;

class ListPerformances extends BaseListResources
{
    protected static string $resource = Performance::class;

    /**
     * Get the header actions available.
     *
     * @return array<int, \Filament\Actions\Action>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->action(function (array $data) {
                    $performances = Arr::get($data, Song::RELATION_PERFORMANCES);
                    PerformanceSongRelationManager::saveArtists(Arr::get($data, PerformanceModel::ATTRIBUTE_SONG), $performances);
                }),
        ];
    }
}
