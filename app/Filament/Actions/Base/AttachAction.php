<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\SongArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ArtistSongRelationManager;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistSong;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\AttachAction as DefaultAttachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachAction.
 */
class AttachAction extends DefaultAttachAction
{
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize('create');

        $this->hidden(fn (BaseRelationManager $livewire) => !($livewire->getRelationship() instanceof BelongsToMany));

        $this->recordSelect(function (BaseRelationManager $livewire) {
            /** @var string */
            $model = $livewire->getTable()->getModel();
            $title = $livewire->getTable()->getRecordTitle(new $model);
            return Select::make('recordId')
                ->label($title)
                ->useScout($model);
        });

        $this->form(function (Form $form, AttachAction $action) {
            return $form
                ->schema([
                    $action->getRecordSelect(),

                    TextInput::make(AnimeResource::ATTRIBUTE_AS)
                        ->label(__('filament.fields.anime.resources.as.name'))
                        ->helperText(__('filament.fields.anime.resources.as.help'))
                        ->visibleOn([
                            AnimeResourceRelationManager::class,
                            ArtistResourceRelationManager::class,
                            SongResourceRelationManager::class,
                            StudioResourceRelationManager::class,
                            ResourceRelationManager::class,
                        ]),

                    TextInput::make(ArtistSong::ATTRIBUTE_AS)
                        ->label(__('filament.fields.artist.songs.as.name'))
                        ->helperText(__('filament.fields.artist.songs.as.help'))
                        ->visibleOn([
                            ArtistSongRelationManager::class,
                            SongArtistRelationManager::class,
                        ]),
                ]);
        });

        $this->after(fn ($livewire, $record) => $this->pivotActionLog('Attach', $livewire, $record));
    }
}
