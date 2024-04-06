<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\Song\Pages\CreateSong;
use App\Filament\Resources\Wiki\Song\Pages\EditSong;
use App\Filament\Resources\Wiki\Song\Pages\ListSongs;
use App\Filament\Resources\Wiki\Song\Pages\ViewSong;
use App\Filament\Resources\Wiki\Song\RelationManagers\ResourceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ResourcesRelationManager;
use App\Models\Wiki\Song as SongModel;
use App\Pivots\Wiki\SongResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Class Song.
 */
class Song extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = SongModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.song');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.songs');
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.wiki');
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(SongModel::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->helperText(__('filament.fields.song.title.help'))
                    ->nullable()
                    ->maxLength(192)
                    ->rules(['nullable', 'max:192']),

                TextInput::make(SongResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song.resources.as.name'))
                    ->helperText(__('filament.fields.song.resources.as.help'))
                    ->visibleOn(SongResourceRelationManager::class),
            ]);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(SongModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(SongModel::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(SongResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song.resources.as.name'))
                    ->visibleOn(SongResourceRelationManager::class),
            ])
            ->defaultSort(SongModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            ResourceSongRelationManager::class,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            []
        );
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the pages available for the resource.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSongs::route('/'),
            'create' => CreateSong::route('/create'),
            'view' => ViewSong::route('/{record}'),
            'edit' => EditSong::route('/{record}/edit'),
        ];
    }
}
