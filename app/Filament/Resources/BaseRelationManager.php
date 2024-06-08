<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Wiki\Anime\RelationManagers\ResourceAnimeRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\ResourceArtistRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\SongArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ArtistSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ResourceSongRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ResourceStudioRelationManager;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\StudioResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class BaseRelationManager.
 */
abstract class BaseRelationManager extends RelationManager
{
    /**
     * The index page of the relation resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions())
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            TrashedFilter::make(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('filament.actions.base.view')),

            EditAction::make()
                ->label(__('filament.actions.base.edit')),

            DetachAction::make()
                ->label(__('filament.actions.base.detach')),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.delete')),

                ForceDeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.forcedelete')),

                RestoreBulkAction::make()
                    ->label(__('filament.bulk_actions.base.restore')),

                DetachBulkAction::make()
                    ->label(__('filament.bulk_actions.base.detach')),
            ]),
        ];
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            AttachAction::make()
                ->hidden(fn (BaseRelationManager $livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                ->recordSelect(function (BaseRelationManager $livewire) {
                    /** @var string */
                    $model = $livewire->getTable()->getModel();
                    $title = $livewire->getTable()->getRecordTitle(new $model);
                    return Select::make('recordId')
                        ->label($title)
                        ->useScout($model);
                })
                ->form(function (Form $form, AttachAction $action) {
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
                                    ResourceAnimeRelationManager::class,
                                    ResourceArtistRelationManager::class,
                                    ResourceSongRelationManager::class,
                                    ResourceStudioRelationManager::class,
                                ]),

                            TextInput::make(ArtistSong::ATTRIBUTE_AS)
                                ->label(__('filament.fields.artist.songs.as.name'))
                                ->helperText(__('filament.fields.artist.songs.as.help'))
                                ->visibleOn([
                                    ArtistSongRelationManager::class,
                                    SongArtistRelationManager::class,
                                ]),
                        ]);
                }),
        ];
    }
}
