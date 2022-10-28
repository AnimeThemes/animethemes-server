<?php

declare(strict_types=1);

namespace App\Nova\Resources\List\Playlist;

use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video as VideoModel;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\List\Playlist;
use App\Nova\Resources\Wiki\Video;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\SearchableRelation;

/**
 * Class Track.
 */
class Track extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = PlaylistTrack::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = PlaylistTrack::ATTRIBUTE_ID;

    /**
     * Get the value that should be displayed to represent the resource.
     *
     * @return string
     */
    public function title(): string
    {
        $track = $this->model();
        if ($track instanceof PlaylistTrack) {
            $video = $track->video;
            if ($video instanceof VideoModel) {
                return $video->getName();
            }
        }

        return parent::title();
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function subtitle(): ?string
    {
        $track = $this->model();
        if ($track instanceof PlaylistTrack) {
            return $track->playlist->getName();
        }

        return null;
    }

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.resources.group.list');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.resources.label.playlist_tracks');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.resources.singularLabel.playlist_track');
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new SearchableRelation(PlaylistTrack::RELATION_VIDEO, VideoModel::ATTRIBUTE_FILENAME),
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with([PlaylistTrack::RELATION_PLAYLIST, PlaylistTrack::RELATION_VIDEO]);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function relatableQuery(NovaRequest $request, $query): Builder
    {
        return $query->with([PlaylistTrack::RELATION_PLAYLIST, PlaylistTrack::RELATION_VIDEO]);
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            BelongsTo::make(__('nova.resources.singularLabel.playlist'), PlaylistTrack::RELATION_PLAYLIST, Playlist::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showOnPreview(),

            BelongsTo::make(__('nova.resources.singularLabel.video'), PlaylistTrack::RELATION_VIDEO, Video::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->withSubtitles()
                ->nullable()
                ->showOnPreview(),

            ID::make(__('nova.fields.base.id'), PlaylistTrack::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            BelongsTo::make(__('nova.fields.playlist_track.previous.name'), PlaylistTrack::RELATION_PREVIOUS, Track::class)
                ->hideFromIndex()
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->help(__('nova.fields.playlist_track.previous.help'))
                ->showOnPreview(),

            BelongsTo::make(__('nova.fields.playlist_track.next.name'), PlaylistTrack::RELATION_NEXT, Track::class)
                ->hideFromIndex()
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->help(__('nova.fields.playlist_track.next.help'))
                ->showOnPreview(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }
}
