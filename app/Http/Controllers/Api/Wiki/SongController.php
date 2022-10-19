<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Song\SongDestroyRequest;
use App\Http\Requests\Api\Wiki\Song\SongForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Song\SongIndexRequest;
use App\Http\Requests\Api\Wiki\Song\SongRestoreRequest;
use App\Http\Requests\Api\Wiki\Song\SongShowRequest;
use App\Http\Requests\Api\Wiki\Song\SongStoreRequest;
use App\Http\Requests\Api\Wiki\Song\SongUpdateRequest;
use App\Models\Wiki\Song;
use Illuminate\Http\JsonResponse;

/**
 * Class SongController.
 */
class SongController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Song::class, 'song');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  SongIndexRequest  $request
     * @return JsonResponse
     */
    public function index(SongIndexRequest $request): JsonResponse
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
     * @param  SongStoreRequest  $request
     * @return JsonResponse
     */
    public function store(SongStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  SongShowRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function show(SongShowRequest $request, Song $song): JsonResponse
    {
        $resource = $request->getQuery()->show($song);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  SongUpdateRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function update(SongUpdateRequest $request, Song $song): JsonResponse
    {
        $resource = $request->getQuery()->update($song);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  SongDestroyRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function destroy(SongDestroyRequest $request, Song $song): JsonResponse
    {
        $resource = $request->getQuery()->destroy($song);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  SongRestoreRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function restore(SongRestoreRequest $request, Song $song): JsonResponse
    {
        $resource = $request->getQuery()->restore($song);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  SongForceDeleteRequest  $request
     * @param  Song  $song
     * @return JsonResponse
     */
    public function forceDelete(SongForceDeleteRequest $request, Song $song): JsonResponse
    {
        return $request->getQuery()->forceDelete($song);
    }
}
