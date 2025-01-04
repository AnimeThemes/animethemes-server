<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Filament\Actions\Models\AssignHashidsAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\Pages\CreatePlaylist;
use App\Filament\Resources\List\Playlist\Pages\EditPlaylist;
use App\Filament\Resources\List\Playlist\Pages\ListPlaylists;
use App\Filament\Resources\List\Playlist\Pages\ViewPlaylist;
use App\Filament\Resources\List\Playlist\RelationManagers\ImagePlaylistRelationManager;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Filament\Resources\List\Playlist\Track;
use App\Models\Auth\User;
use App\Models\List\Playlist as PlaylistModel;
use App\Models\List\Playlist\PlaylistTrack;
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
    public static function getRecordSlug(): string
    {
        return 'playlists';
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
        return PlaylistModel::ATTRIBUTE_NAME;
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
                BelongsTo::make(PlaylistModel::ATTRIBUTE_USER)
                    ->resource(UserResource::class),

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

                BelongsTo::make(PlaylistModel::ATTRIBUTE_FIRST)
                    ->resource(Track::class)
                    ->label(__('filament.fields.playlist.first.name')),

                BelongsTo::make(PlaylistModel::ATTRIBUTE_LAST)
                    ->resource(Track::class)
                    ->label(__('filament.fields.playlist.last.name')),

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
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(PlaylistModel::RELATION_USER, UserResource::class),

                TextColumn::make(PlaylistModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(PlaylistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column) => $column->getState())
                    ->copyableWithMessage(),

                TextColumn::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->formatStateUsing(fn (PlaylistVisibility $state) => $state->localize()),

                TextColumn::make(PlaylistModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist.hashid.name'))
                    ->copyableWithMessage(),

                BelongsToColumn::make(PlaylistModel::RELATION_FIRST, Track::class)
                    ->label(__('filament.fields.playlist.first.name')),

                BelongsToColumn::make(PlaylistModel::RELATION_LAST, Track::class)
                    ->label(__('filament.fields.playlist.last.name')),

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
                            ->urlToRelated(UserResource::class, PlaylistModel::RELATION_USER),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.playlist.name.name'))
                            ->limit(30)
                            ->copyableWithMessage(),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                            ->label(__('filament.fields.playlist.visibility.name'))
                            ->formatStateUsing(fn (PlaylistVisibility $state) => $state->localize()),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist.hashid.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_FIRST)
                            ->label(__('filament.fields.playlist.first.name'))
                            ->urlToRelated(Track::class, PlaylistModel::RELATION_FIRST),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_LAST)
                            ->label(__('filament.fields.playlist.last.name'))
                            ->urlToRelated(Track::class, PlaylistModel::RELATION_LAST),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.fields.playlist.description.name'))
                            ->markdown()
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        ImagePlaylistRelationManager::class,
                        TrackPlaylistRelationManager::class,
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
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [
                AssignHashidsAction::make('assign-hashids')
                    ->setConnection('playlists')
                    ->authorize('update', PlaylistModel::class),
            ],
        );
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
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
