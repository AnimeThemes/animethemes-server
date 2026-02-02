<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\List\AssignHashidsAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist\RelationManagers\TrackPlaylistRelationManager;
use App\Filament\Resources\List\Playlist\Track\Pages\ListTracks;
use App\Filament\Resources\List\Playlist\Track\Pages\ViewTrack;
use App\Filament\Resources\List\PlaylistResource;
use App\Filament\Resources\Wiki\Anime\Theme\EntryResource;
use App\Filament\Resources\Wiki\VideoResource;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Closure;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class TrackResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = PlaylistTrack::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.playlist_track');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.playlist_tracks');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::LIST;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedPlay;
    }

    public static function getRecordSlug(): string
    {
        return 'tracks';
    }

    public static function getRecordTitleAttribute(): string
    {
        return PlaylistTrack::ATTRIBUTE_HASHID;
    }

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                BelongsTo::make(PlaylistTrack::ATTRIBUTE_PLAYLIST)
                    ->resource(PlaylistResource::class)
                    ->required()
                    ->hiddenOn([TrackPlaylistRelationManager::class]),

                BelongsTo::make(PlaylistTrack::ATTRIBUTE_ENTRY)
                    ->resource(EntryResource::class)
                    ->live(true)
                    ->rules([
                        fn (Get $get): Closure => (fn (): array => [
                            Rule::when(
                                filled($get(PlaylistTrack::RELATION_ENTRY)) && filled($get(PlaylistTrack::RELATION_VIDEO)),
                                [
                                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $get(PlaylistTrack::RELATION_VIDEO)),
                                ]
                            ),
                        ]),
                    ]),

                Select::make(PlaylistTrack::ATTRIBUTE_VIDEO)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->relationship(PlaylistTrack::RELATION_VIDEO, Video::ATTRIBUTE_FILENAME)
                    ->rules([
                        fn (Get $get): Closure => (fn (): array => [
                            Rule::when(
                                filled($get(PlaylistTrack::RELATION_ENTRY)) && filled($get(PlaylistTrack::RELATION_VIDEO)),
                                [
                                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $get(PlaylistTrack::RELATION_ENTRY)),
                                ]
                            ),
                        ]),
                    ])
                    ->options(fn (Get $get) => Video::query()
                        ->whereHas(Video::RELATION_ANIMETHEMEENTRIES, function ($query) use ($get): void {
                            /** @phpstan-ignore-next-line */
                            $query->where(AnimeThemeEntry::TABLE.'.'.AnimeThemeEntry::ATTRIBUTE_ID, $get(PlaylistTrack::ATTRIBUTE_ENTRY));
                        })
                        ->get()
                        ->mapWithKeys(fn (Video $video): array => [$video->getKey() => $video->getName()])
                        ->toArray()),

                TextInput::make(PlaylistTrack::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name'))
                    ->helperText(__('filament.fields.playlist_track.hashid.help'))
                    ->readOnly(),

                BelongsTo::make(PlaylistTrack::ATTRIBUTE_PREVIOUS)
                    ->resource(TrackResource::class)
                    ->label(__('filament.fields.playlist_track.previous.name'))
                    ->helperText(__('filament.fields.playlist_track.previous.help'))
                    ->searchable(),

                BelongsTo::make(PlaylistTrack::ATTRIBUTE_NEXT)
                    ->resource(TrackResource::class)
                    ->label(__('filament.fields.playlist_track.next.name'))
                    ->helperText(__('filament.fields.playlist_track.next.help')),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                BelongsToColumn::make(PlaylistTrack::RELATION_PLAYLIST, PlaylistResource::class)
                    ->hiddenOn(TrackPlaylistRelationManager::class),

                BelongsToColumn::make(PlaylistTrack::RELATION_ENTRY, EntryResource::class),

                BelongsToColumn::make(PlaylistTrack::RELATION_VIDEO, VideoResource::class),

                TextColumn::make(PlaylistTrack::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(PlaylistTrack::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name')),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        BelongsToEntry::make(PlaylistTrack::RELATION_PLAYLIST, PlaylistResource::class),

                        TextEntry::make(PlaylistTrack::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist_track.hashid.name')),

                        TextEntry::make(PlaylistTrack::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(PlaylistTrack::RELATION_ENTRY, EntryResource::class),

                        BelongsToEntry::make(PlaylistTrack::RELATION_VIDEO, VideoResource::class),

                        BelongsToEntry::make(PlaylistTrack::RELATION_PREVIOUS, TrackResource::class)
                            ->label(__('filament.fields.playlist_track.previous.name')),

                        BelongsToEntry::make(PlaylistTrack::RELATION_NEXT, TrackResource::class)
                            ->label(__('filament.fields.playlist_track.next.name')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            AssignHashidsAction::make()
                ->setConnection('playlists'),
        ];
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListTracks::route('/'),
            'view' => ViewTrack::route('/{record:track_id}'),
        ];
    }
}
