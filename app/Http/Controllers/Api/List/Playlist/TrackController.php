<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\List\Playlist\Track\DestroyTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\ForceDeleteTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\RestoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\List\Playlist\Track\UpdateTrackAction;
use App\Actions\Http\Api\ShowAction;
use App\Constants\Config\FlagConstants;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Models\List\PlaylistExceedsTrackLimit;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class TrackController.
 */
class TrackController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');

        $isPlaylistManagementAllowed = Str::of('is_feature_enabled:')
            ->append(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED)
            ->append(',Playlist Management Disabled')
            ->__toString();

        $this->middleware($isPlaylistManagementAllowed)->except(['index', 'show']);
        $this->middleware(PlaylistExceedsTrackLimit::class)->only(['store', 'restore']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  Playlist  $playlist
     * @param  IndexAction  $action
     * @return TrackCollection
     */
    public function index(IndexRequest $request, Playlist $playlist, IndexAction $action): TrackCollection
    {
        $query = new Query($request->validated());

        $builder = PlaylistTrack::query()->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new TrackCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Playlist  $playlist
     * @param  StoreTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function store(StoreRequest $request, Playlist $playlist, StoreTrackAction $action): TrackResource
    {
        $track = $action->store($playlist, PlaylistTrack::query(), $request->validated());

        return new TrackResource($track, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  ShowAction  $action
     * @return TrackResource
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function show(ShowRequest $request, Playlist $playlist, PlaylistTrack $track, ShowAction $action): TrackResource
    {
        $query = new Query($request->validated());

        $show = $action->show($track, $query, $request->schema());

        return new TrackResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  UpdateTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function update(UpdateRequest $request, Playlist $playlist, PlaylistTrack $track, UpdateTrackAction $action): TrackResource
    {
        $updated = $action->update($playlist, $track, $request->validated());

        return new TrackResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  DestroyTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function destroy(Playlist $playlist, PlaylistTrack $track, DestroyTrackAction $action): TrackResource
    {
        $deleted = $action->destroy($playlist, $track);

        return new TrackResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  RestoreTrackAction  $action
     * @return TrackResource
     *
     * @throws Exception
     */
    public function restore(Playlist $playlist, PlaylistTrack $track, RestoreTrackAction $action): TrackResource
    {
        $restored = $action->restore($playlist, $track);

        return new TrackResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  ForceDeleteTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function forceDelete(Playlist $playlist, PlaylistTrack $track, ForceDeleteTrackAction $action): JsonResponse
    {
        $message = $action->forceDelete($playlist, $track);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
