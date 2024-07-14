<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Song\AttachSongResourceAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Artist\RelationManagers\SongArtistRelationManager;
use App\Filament\Resources\Wiki\ExternalResource\RelationManagers\SongResourceRelationManager;
use App\Filament\Resources\Wiki\Song\Pages\CreateSong;
use App\Filament\Resources\Wiki\Song\Pages\EditSong;
use App\Filament\Resources\Wiki\Song\Pages\ListSongs;
use App\Filament\Resources\Wiki\Song\Pages\ViewSong;
use App\Filament\Resources\Wiki\Song\RelationManagers\ArtistSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ResourceSongRelationManager;
use App\Filament\Resources\Wiki\Song\RelationManagers\ThemeSongRelationManager;
use App\Models\Wiki\Song as SongModel;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\SongResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.songs');
    }

    /**
     * Determine if the resource can globally search.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function canGloballySearch(): bool
    {
        return true;
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'songs';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return SongModel::ATTRIBUTE_TITLE;
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
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                TextInput::make(SongResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song.resources.as.name'))
                    ->helperText(__('filament.fields.song.resources.as.help'))
                    ->visibleOn(SongResourceRelationManager::class)
                    ->placeholder('-'),

                TextInput::make(ArtistSong::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.songs.as.name'))
                    ->helperText(__('filament.fields.artist.songs.as.help'))
                    ->visibleOn(SongArtistRelationManager::class)
                    ->placeholder('-'),
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
                    ->sortable(),

                TextColumn::make(SongModel::ATTRIBUTE_TITLE)
                    ->label(__('filament.fields.song.title.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(SongResource::ATTRIBUTE_AS)
                    ->label(__('filament.fields.song.resources.as.name'))
                    ->visibleOn(SongResourceRelationManager::class)
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make(ArtistSong::ATTRIBUTE_AS)
                    ->label(__('filament.fields.artist.songs.as.name'))
                    ->visibleOn(SongArtistRelationManager::class)
                    ->toggleable()
                    ->placeholder('-'),
            ])
            ->searchable();
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(SongModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(SongModel::ATTRIBUTE_TITLE)
                            ->label(__('filament.fields.song.title.name'))
                            ->copyableWithMessage(),
                    ]),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        ArtistSongRelationManager::class,
                        ThemeSongRelationManager::class,
                        ResourceSongRelationManager::class,
                    ],
                    parent::getBaseRelations(),
                )
            ),
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
            [],
            parent::getFilters(),
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
            [
                ActionGroup::make([
                    AttachSongResourceAction::make('attach-song-resource'),
                ])
            ],
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
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
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
            'view' => ViewSong::route('/{record:song_id}'),
            'edit' => EditSong::route('/{record:song_id}/edit'),
        ];
    }
}
