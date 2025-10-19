<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Song;

use App\Filament\Resources\Wiki\Song\Performance\Schemas\PerformanceForm;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Models\Wiki\Song;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Set;

class LoadArtistsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'load-artists';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.fields.song.load_artists.name'));

        $this->action(function (Set $set, $state): void {
            /** @var Song|null $song */
            $song = Song::query()->find($state);
            $set(PerformanceForm::REPEATER_PERFORMANCES, PerformanceSongRelationManager::formatArtists($song));
        });
    }
}
