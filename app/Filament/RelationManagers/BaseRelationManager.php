<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\Wiki\Artist\RelationManagers\SongArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\AnimeResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\ArtistResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\StudioResourceRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ArtistSongRelationManager;
use App\Models\BaseModel;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistSong;
use DateTime;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * Class BaseRelationManager.
 */
abstract class BaseRelationManager extends RelationManager
{
    protected static bool $isLazy = false;

    /**
     * The actions should appear in the view page.
     * 
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return false;
    }

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
            ->columns(array_merge(
                $table->getColumns(),
                [
                    TextColumn::make(BasePivot::ATTRIBUTE_CREATED_AT)
                        ->label(__('filament.fields.base.created_at'))
                        ->hidden(fn ($livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                        ->formatStateUsing(function (BaseModel $record) {
                            $pivot = current($record->getRelations());
                            return (new DateTime(Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_CREATED_AT)))->format('M j, Y H:i:s');
                        }),

                    TextColumn::make(BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->label(__('filament.fields.base.updated_at'))
                        ->hidden(fn ($livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                        ->formatStateUsing(function (BaseModel $record) {
                            $pivot = current($record->getRelations());
                            return (new DateTime(Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_UPDATED_AT)))->format('M j, Y H:i:s');
                        }),
                ],
            ))
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions())
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
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
        return [];
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
            DetachBulkAction::make()
                ->label(__('filament.bulk_actions.base.detach'))
                ->authorize('forcedeleteany'),
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
                ->authorize('create')
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
                }),
        ];
    }
}
