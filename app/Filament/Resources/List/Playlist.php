<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Filament\Actions\Models\AssignHashidsAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\Pages\CreatePlaylist;
use App\Filament\Resources\List\Playlist\Pages\EditPlaylist;
use App\Filament\Resources\List\Playlist\Pages\ListPlaylists;
use App\Filament\Resources\List\Playlist\Pages\ViewPlaylist;
use App\Filament\Resources\List\Playlist\Track;
use App\Filament\Resources\List\Playlist\RelationManagers\ImagePlaylistRelationManager;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Models\Auth\User as UserModel;
use App\Models\List\Playlist as PlaylistModel;
use App\Models\List\Playlist\PlaylistTrack as TrackModel;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Enum;

/**
 * Class Playlist.
 */
class Playlist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = PlaylistModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.playlist');
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
        return __('filament.resources.label.playlists');
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
        return __('filament.resources.group.list');
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
        return __('filament.resources.icon.playlists');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return static::getDefaultSlug().'playlists';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        return PlaylistModel::ATTRIBUTE_ID;
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
                Select::make(PlaylistModel::ATTRIBUTE_USER)
                    ->label(__('filament.resources.singularLabel.user'))
                    ->relationship(PlaylistModel::RELATION_USER, UserModel::ATTRIBUTE_NAME)
                    ->searchable(),

                TextInput::make(PlaylistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->helperText(__('filament.fields.playlist.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192']),

                Select::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->helperText(__('filament.fields.playlist.visibility.help'))
                    ->options(PlaylistVisibility::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(PlaylistVisibility::class)]),

                TextInput::make(PlaylistModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist.hashid.name'))
                    ->helperText(__('filament.fields.playlist.hashid.help'))
                    ->readOnly(),

                Select::make(PlaylistModel::ATTRIBUTE_FIRST)
                    ->label(__('filament.fields.playlist.first.name'))
                    ->relationship(PlaylistModel::RELATION_FIRST, TrackModel::ATTRIBUTE_HASHID)
                    ->searchable(),

                Select::make(PlaylistModel::ATTRIBUTE_LAST)
                    ->label(__('filament.fields.playlist.last.name'))
                    ->relationship(PlaylistModel::RELATION_LAST, TrackModel::ATTRIBUTE_HASHID)
                    ->searchable(),

                Textarea::make(PlaylistModel::ATTRIBUTE_DESCRIPTION)
                    ->label(__('filament.fields.playlist.description.name'))
                    ->helperText(__('filament.fields.playlist.description.help'))
                    ->nullable()
                    ->maxLength(1000)
                    ->rules(['nullable', 'max:1000'])
                    ->columnSpanFull(),
            ])
            ->columns(2);
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
                TextColumn::make(PlaylistModel::ATTRIBUTE_USER)
                    ->label(__('filament.resources.singularLabel.user'))
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(UserResource::class, PlaylistModel::RELATION_USER),

                TextColumn::make(PlaylistModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(PlaylistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => $state->localize()),

                TextColumn::make(PlaylistModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist.hashid.name'))
                    ->toggleable()
                    ->placeholder('-')
                    ->copyableWithMessage(),

                TextColumn::make(PlaylistModel::ATTRIBUTE_FIRST)
                    ->label(__('filament.fields.playlist.first.name'))
                    ->visibleOn(['create', 'edit', 'view'])
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(Track::class, PlaylistModel::RELATION_FIRST),

                TextColumn::make(PlaylistModel::ATTRIBUTE_LAST)
                    ->label(__('filament.fields.playlist.last.name'))
                    ->visibleOn(['create', 'edit', 'view'])
                    ->toggleable()
                    ->placeholder('-')
                    ->urlToRelated(Track::class, PlaylistModel::RELATION_LAST),

                TextColumn::make(PlaylistModel::ATTRIBUTE_DESCRIPTION)
                    ->label(__('filament.fields.playlist.description.name'))
                    ->hidden(),
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
                        TextEntry::make(PlaylistModel::ATTRIBUTE_USER)
                            ->label(__('filament.resources.singularLabel.user'))
                            ->placeholder('-')
                            ->urlToRelated(UserResource::class, PlaylistModel::RELATION_USER),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.playlist.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                            ->label(__('filament.fields.playlist.visibility.name'))
                            ->formatStateUsing(fn ($state) => $state->localize()),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist.hashid.name'))
                            ->placeholder('-')
                            ->copyableWithMessage(),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_FIRST)
                            ->label(__('filament.fields.playlist.first.name'))
                            ->placeholder('-')
                            ->urlToRelated(Track::class, PlaylistModel::RELATION_FIRST),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_LAST)
                            ->label(__('filament.fields.playlist.last.name'))
                            ->placeholder('-')
                            ->urlToRelated(Track::class, PlaylistModel::RELATION_LAST),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.fields.playlist.description.name'))
                            ->placeholder('-')
                            ->copyableWithMessage()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

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
            RelationGroup::make(static::getLabel(), [
                ImagePlaylistRelationManager::class,
                TrackPlaylistRelationManager::class,
            ]),
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
            [
                AssignHashidsAction::make('assign-hashids')
                    ->label(__('filament.actions.models.assign_hashids.name'))
                    ->setConnection('playlists')
                    ->requiresConfirmation()
                    ->authorize('update', PlaylistModel::class),
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
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPlaylists::route('/'),
            'create' => CreatePlaylist::route('/create'),
            'view' => ViewPlaylist::route('/{record:playlist_id}'),
            'edit' => EditPlaylist::route('/{record:playlist_id}/edit'),
        ];
    }
}
