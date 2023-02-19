<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\List\Playlist\Track\StoreTrackAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\List\Playlist\Collection\TrackCollection;
use App\Http\Resources\List\Playlist\Resource\TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * @param  UpdateAction  $action
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(UpdateRequest $request, Playlist $playlist, PlaylistTrack $track, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($track, $request->validated());

        $resource = new TrackResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  DestroyAction  $action
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function destroy(Request $request, Playlist $playlist, PlaylistTrack $track, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($track);

        $resource = new TrackResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  RestoreAction  $action
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(Request $request, Playlist $playlist, PlaylistTrack $track, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($track);

        $resource = new TrackResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function forceDelete(Playlist $playlist, PlaylistTrack $track, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($track);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
