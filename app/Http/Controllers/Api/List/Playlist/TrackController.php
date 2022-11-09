<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\Track\TrackDestroyRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackForceDeleteRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackIndexRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackRestoreRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackShowRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackStoreRequest;
use App\Http\Requests\Api\List\Playlist\Track\TrackUpdateRequest;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;

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
     * @param  TrackIndexRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function index(TrackIndexRequest $request, Playlist $playlist): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  TrackStoreRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function store(TrackStoreRequest $request, Playlist $playlist): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  TrackShowRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function show(TrackShowRequest $request, Playlist $playlist, PlaylistTrack $track): JsonResponse
    {
        $resource = $request->getQuery()->show($track);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  TrackUpdateRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(TrackUpdateRequest $request, Playlist $playlist, PlaylistTrack $track): JsonResponse
    {
        $resource = $request->getQuery()->update($track);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  TrackDestroyRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function destroy(TrackDestroyRequest $request, Playlist $playlist, PlaylistTrack $track): JsonResponse
    {
        $resource = $request->getQuery()->destroy($track);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  TrackRestoreRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function restore(TrackRestoreRequest $request, Playlist $playlist, PlaylistTrack $track): JsonResponse
    {
        $resource = $request->getQuery()->restore($track);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  TrackForceDeleteRequest  $request
     * @param  Playlist  $playlist
     * @param  PlaylistTrack  $track
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function forceDelete(TrackForceDeleteRequest $request, Playlist $playlist, PlaylistTrack $track): JsonResponse
    {
        return $request->getQuery()->forceDelete($track);
    }
}
