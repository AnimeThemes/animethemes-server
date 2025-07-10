<?php

declare(strict_types=1);

namespace App\Filament\Resources\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Filament\Actions\Models\List\AssignHashidsAction;
use App\Filament\Actions\Models\List\Playlist\FixPlaylistAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\Pages\ListPlaylists;
use App\Filament\Resources\List\Playlist\Pages\ViewPlaylist;
use App\Filament\Resources\List\Playlist\RelationManagers\ImagePlaylistRelationManager;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Filament\Resources\List\Playlist\Track;
use App\Models\List\Playlist as PlaylistModel;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Playlist.
 */
class Playlist extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = PlaylistModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
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
    public static function getPluralModelLabel(): string
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
        return __('filament-icons.resources.playlists');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
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
     * Get the eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            PlaylistModel::RELATION_USER,
            PlaylistModel::RELATION_FIRST,
            PlaylistModel::RELATION_LAST,
        ]);
    }

    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(PlaylistModel::ATTRIBUTE_USER)
                    ->resource(UserResource::class),

                TextInput::make(PlaylistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.playlist.name.name'))
                    ->helperText(__('filament.fields.playlist.name.help'))
                    ->required()
                    ->maxLength(192),

                Select::make(PlaylistModel::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.playlist.visibility.name'))
                    ->helperText(__('filament.fields.playlist.visibility.help'))
                    ->options(PlaylistVisibility::class)
                    ->required(),

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
                    ->maxLength(1000)
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
                    ->copyableWithMessage()
                    ->searchable(isIndividual: true),

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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(PlaylistModel::RELATION_USER, UserResource::class),

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

                        BelongsToEntry::make(PlaylistModel::RELATION_FIRST, Track::class)
                            ->label(__('filament.fields.playlist.first.name')),

                        BelongsToEntry::make(PlaylistModel::RELATION_LAST, Track::class)
                            ->label(__('filament.fields.playlist.last.name')),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PlaylistModel::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.fields.playlist.description.name'))
                            ->markdown()
                            ->copyableWithMessage()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
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
            RelationGroup::make(static::getModelLabel(), [
                ImagePlaylistRelationManager::class,
                TrackPlaylistRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            AssignHashidsAction::make()
                ->setConnection('playlists'),

            FixPlaylistAction::make(),
        ];
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
            'view' => ViewPlaylist::route('/{record:playlist_id}'),
        ];
    }
}
