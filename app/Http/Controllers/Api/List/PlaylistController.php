<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\PlaylistDestroyRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistForceDeleteRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistIndexRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistRestoreRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistShowRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistStoreRequest;
use App\Http\Requests\Api\List\Playlist\PlaylistUpdateRequest;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;

/**
 * Class PlaylistController.
 */
class PlaylistController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::class, 'playlist');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  PlaylistIndexRequest  $request
     * @return JsonResponse
     */
    public function index(PlaylistIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  PlaylistStoreRequest  $request
     * @return JsonResponse
     */
    public function store(PlaylistStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  PlaylistShowRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     */
    public function show(PlaylistShowRequest $request, Playlist $playlist): JsonResponse
    {
        $resource = $request->getQuery()->show($playlist);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  PlaylistUpdateRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     */
    public function update(PlaylistUpdateRequest $request, Playlist $playlist): JsonResponse
    {
        $resource = $request->getQuery()->update($playlist);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  PlaylistDestroyRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     */
    public function destroy(PlaylistDestroyRequest $request, Playlist $playlist): JsonResponse
    {
        $resource = $request->getQuery()->destroy($playlist);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  PlaylistRestoreRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     */
    public function restore(PlaylistRestoreRequest $request, Playlist $playlist): JsonResponse
    {
        $resource = $request->getQuery()->restore($playlist);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  PlaylistForceDeleteRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     */
    public function forceDelete(PlaylistForceDeleteRequest $request, Playlist $playlist): JsonResponse
    {
        return $request->getQuery()->forceDelete($playlist);
    }
}
