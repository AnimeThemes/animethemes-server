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
use Illuminate\Http\Request;
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
     * @return JsonResponse
     */
    public function index(IndexRequest $request, Playlist $playlist, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $builder = PlaylistTrack::query()->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        $collection = new TrackCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Playlist  $playlist
     * @param  StoreTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreRequest $request, Playlist $playlist, StoreTrackAction $action): JsonResponse
    {
        $track = $action->store($playlist, PlaylistTrack::query(), $request->validated());

        $resource = new TrackResource($track, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  ShowAction  $action
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function show(ShowRequest $request, Playlist $playlist, PlaylistTrack $track, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($track, $query, $request->schema());

        $resource = new TrackResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  UpdateTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateRequest $request, Playlist $playlist, PlaylistTrack $track, UpdateTrackAction $action): JsonResponse
    {
        $updated = $action->update($playlist, $track, $request->validated());

        $resource = new TrackResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  DestroyTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Request $request, Playlist $playlist, PlaylistTrack $track, DestroyTrackAction $action): JsonResponse
    {
        $deleted = $action->destroy($playlist, $track);

        $resource = new TrackResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  RestoreTrackAction  $action
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function restore(Request $request, Playlist $playlist, PlaylistTrack $track, RestoreTrackAction $action): JsonResponse
    {
        $restored = $action->restore($playlist, $track);

        $resource = new TrackResource($restored, new Query());

        return $resource->toResponse($request);
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
