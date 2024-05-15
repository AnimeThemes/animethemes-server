<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist;

use App\Filament\Actions\Models\AssignHashidsAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\Playlist as PlaylistResource;
use App\Filament\Resources\List\Playlist\Track\Pages\CreateTrack;
use App\Filament\Resources\List\Playlist\Track\Pages\EditTrack;
use App\Filament\Resources\List\Playlist\Track\Pages\ListTracks;
use App\Filament\Resources\List\Playlist\Track\Pages\ViewTrack;
use App\Filament\Resources\Wiki\Video as VideoResource;
use App\Models\List\Playlist as PlaylistModel;
use App\Models\List\Playlist\PlaylistTrack as TrackModel;
use App\Models\Wiki\Video as VideoModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;

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
        return __('filament.resources.icon.playlist_tracks');
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
        return static::getDefaultSlug().'tracks';
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
        return TrackModel::ATTRIBUTE_ID;
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
                Select::make(TrackModel::ATTRIBUTE_PLAYLIST)
                    ->label(__('filament.resources.singularLabel.playlist'))
                    ->relationship(TrackModel::RELATION_PLAYLIST, PlaylistModel::ATTRIBUTE_NAME)
                    ->searchable()
                    ->createOptionForm(PlaylistResource::form($form)->getComponents()),

                Select::make(TrackModel::ATTRIBUTE_VIDEO)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->relationship(TrackModel::RELATION_VIDEO, VideoModel::ATTRIBUTE_FILENAME)
                    ->searchable(),
                
                TextInput::make(TrackModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name'))
                    ->helperText(__('filament.fields.playlist_track.hashid.help'))
                    ->readOnly(),

                Select::make(TrackModel::ATTRIBUTE_PREVIOUS)
                    ->label(__('filament.fields.playlist_track.previous.name'))
                    ->helperText(__('filament.fields.playlist_track.previous.help'))
                    ->relationship(TrackModel::RELATION_PREVIOUS, TrackModel::ATTRIBUTE_HASHID)
                    ->searchable(),

                Select::make(TrackModel::ATTRIBUTE_NEXT)
                    ->label(__('filament.fields.playlist_track.next.name'))
                    ->helperText(__('filament.fields.playlist_track.next.help'))
                    ->relationship(TrackModel::RELATION_NEXT, TrackModel::ATTRIBUTE_HASHID)
                    ->searchable(),
            ])
            ->columns(1);
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
                TextColumn::make(TrackModel::RELATION_PLAYLIST.'.'.PlaylistModel::ATTRIBUTE_NAME)
                    ->label(__('filament.resources.singularLabel.playlist'))
                    ->toggleable()
                    ->urlToRelated(PlaylistResource::class, TrackModel::RELATION_PLAYLIST),

                TextColumn::make(TrackModel::RELATION_VIDEO.'.'.VideoModel::ATTRIBUTE_FILENAME)
                    ->label(__('filament.resources.singularLabel.video'))
                    ->toggleable()
                    ->urlToRelated(VideoResource::class, TrackModel::RELATION_VIDEO),

                TextColumn::make(TrackModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make(TrackModel::ATTRIBUTE_HASHID)
                    ->label(__('filament.fields.playlist_track.hashid.name'))
                    ->toggleable()
                    ->placeholder('-'),
            ])
            ->defaultSort(TrackModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
                        TextEntry::make(TrackModel::RELATION_PLAYLIST.'.'.PlaylistModel::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.playlist'))
                            ->urlToRelated(PlaylistResource::class, TrackModel::RELATION_PLAYLIST),

                        TextEntry::make(TrackModel::ATTRIBUTE_HASHID)
                            ->label(__('filament.fields.playlist_track.hashid.name'))
                            ->placeholder('-'),

                        TextEntry::make(TrackModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(TrackModel::RELATION_VIDEO.'.'.VideoModel::ATTRIBUTE_FILENAME)
                            ->label(__('filament.resources.singularLabel.video'))
                            ->urlToRelated(VideoResource::class, TrackModel::RELATION_VIDEO),

                        TextEntry::make(TrackModel::RELATION_PREVIOUS.'.'.TrackModel::RELATION_VIDEO.'.'.VideoModel::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.playlist_track.previous.name'))
                            ->urlToRelated(Track::class, TrackModel::RELATION_PREVIOUS),
        
                        TextEntry::make(TrackModel::RELATION_NEXT.'.'.TrackModel::RELATION_VIDEO.'.'.VideoModel::ATTRIBUTE_FILENAME)
                            ->label(__('filament.fields.playlist_track.next.name'))
                            ->urlToRelated(Track::class, TrackModel::RELATION_NEXT),
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
        return [];
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
                    ->authorize('update', TrackModel::class),
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

   // protected static bool $shouldSkipAuthorization = true;

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
