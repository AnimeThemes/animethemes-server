<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist;

use App\Filament\Actions\Models\List\AssignHashidsAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist as PlaylistResource;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Filament\Resources\List\Playlist\Track\Pages\CreateTrack;
use App\Filament\Resources\List\Playlist\Track\Pages\EditTrack;
use App\Filament\Resources\List\Playlist\Track\Pages\ListTracks;
use App\Filament\Resources\List\Playlist\Track\Pages\ViewTrack;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\List\Playlist\PlaylistTrack as TrackModel;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video as VideoModel;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

/**
 * Class Track.
 */
class Track extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = TrackModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.playlist_track');
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
        return __('filament.resources.label.playlist_tracks');
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
        return __('filament-icons.resources.playlist_tracks');
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
        return 'tracks';
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
        return TrackModel::ATTRIBUTE_HASHID;
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
            PlaylistTrack::RELATION_PLAYLIST,
            PlaylistTrack::RELATION_VIDEO,
            'animethemeentry.anime',
            'animethemeentry.animetheme.group',
        ]);
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
                BelongsTo::make(TrackModel::ATTRIBUTE_PLAYLIST)
                    ->resource(PlaylistResource::class)
                    ->required()
                    ->rules(['required'])
                    ->hiddenOn([TrackPlaylistRelationManager::class]),

                BelongsTo::make(TrackModel::ATTRIBUTE_ENTRY)
                    ->resource(Entry::class)
                    ->live(true)
                    ->rules([
                        fn (Get $get) => function () use ($get) {
                            return [
                                Rule::when(
                                    !empty($get(TrackModel::RELATION_ENTRY)) && !empty($get(TrackModel::RELATION_VIDEO)),
                                    [
                                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $get(TrackModel::RELATION_VIDEO)),
                                    ]
                                )
                            ];
                        }
                    ]),

                Select::make(TrackModel::ATTRIBUTE_VIDEO)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->relationship(TrackModel::RELATION_VIDEO, VideoModel::ATTRIBUTE_FILENAME)
                    ->rules([
                        fn (Get $get) => function () use ($get) {
                            return [
                                Rule::when(
                                    !empty($get(TrackModel::RELATION_ENTRY)) && !empty($get(TrackModel::RELATION_VIDEO)),
                                    [
                                        Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                                            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $get(TrackModel::RELATION_ENTRY)),
                                    ]
                                )
                            ];
                        }
                    ])
                    ->options(function (Get $get) {
                        return VideoModel::query()
                            ->whereHas(VideoModel::RELATION_ANIMETHEMEENTRIES, function ($query) use ($get) {
                                $query->where(AnimeThemeEntry::TABLE . '.' . AnimeThemeEntry::ATTRIBUTE_ID, $get(TrackModel::ATTRIBUTE_ENTRY));
                            })
                            ->get()
                            ->mapWithKeys(fn (VideoModel $video) => [$video->getKey() => $video->getName()])
                            ->toArray();
                    }),

                TextInput::make(TrackModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name'))
                    ->helperText(__('filament.fields.playlist_track.hashid.help'))
                    ->readOnly(),

                BelongsTo::make(TrackModel::ATTRIBUTE_PREVIOUS)
                    ->resource(Track::class)
                    ->label(__('filament.fields.playlist_track.previous.name'))
                    ->helperText(__('filament.fields.playlist_track.previous.help'))
                    ->searchable(),

                BelongsTo::make(TrackModel::ATTRIBUTE_NEXT)
                    ->resource(Track::class)
                    ->label(__('filament.fields.playlist_track.next.name'))
                    ->helperText(__('filament.fields.playlist_track.next.help')),
            ])
            ->columns(1);
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
                BelongsToColumn::make(TrackModel::RELATION_PLAYLIST, PlaylistResource::class)
                    ->hiddenOn(TrackPlaylistRelationManager::class),

                BelongsToColumn::make(TrackModel::RELATION_ENTRY, Entry::class),

                BelongsToColumn::make(TrackModel::RELATION_VIDEO, VideoResource::class),

                TextColumn::make(TrackModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(TrackModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name')),
            ]);
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
                        BelongsToEntry::make(TrackModel::RELATION_PLAYLIST, PlaylistResource::class),

                        TextEntry::make(TrackModel::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist_track.hashid.name')),

                        TextEntry::make(TrackModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(TrackModel::RELATION_ENTRY, Entry::class),

                        BelongsToEntry::make(TrackModel::RELATION_VIDEO, VideoResource::class),

                        BelongsToEntry::make(TrackModel::RELATION_PREVIOUS, Track::class)
                            ->label(__('filament.fields.playlist_track.previous.name')),

                        BelongsToEntry::make(TrackModel::RELATION_NEXT, Track::class)
                            ->label(__('filament.fields.playlist_track.next.name')),
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
            RelationGroup::make(
                static::getLabel(),
                array_merge(
                    [],
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
                    ->authorize('update', TrackModel::class),
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
            'index' => ListTracks::route('/'),
            'create' => CreateTrack::route('/create'),
            'view' => ViewTrack::route('/{record:track_id}'),
            'edit' => EditTrack::route('/{record:track_id}/edit'),
        ];
    }
}
