<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Performance\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\Performance;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

/**
 * Class ListPerformances.
 */
class ListPerformances extends BaseListResources
{
    protected static string $resource = Performance::class;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('new performance')
                ->schema(fn (Schema $schema) => Performance::form($schema)->getComponents())
                ->authorize('create', PerformanceModel::class)
                ->action(function (array $data) {
                    $performances = Arr::get($data, Song::RELATION_PERFORMANCES);
                    PerformanceSongRelationManager::saveArtists(Arr::get($data, PerformanceModel::ATTRIBUTE_SONG), $performances);
                }),
        ];
    }
}
