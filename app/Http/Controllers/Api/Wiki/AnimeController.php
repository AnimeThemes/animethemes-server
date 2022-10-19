<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Anime\AnimeDestroyRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeRestoreRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeShowRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeStoreRequest;
use App\Http\Requests\Api\Wiki\Anime\AnimeUpdateRequest;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeController.
 */
class AnimeController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  AnimeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(AnimeIndexRequest $request): JsonResponse
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
     * @param  AnimeStoreRequest  $request
     * @return JsonResponse
     */
    public function store(AnimeStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AnimeShowRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function show(AnimeShowRequest $request, Anime $anime): JsonResponse
    {
        $resource = $request->getQuery()->show($anime);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  AnimeUpdateRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function update(AnimeUpdateRequest $request, Anime $anime): JsonResponse
    {
        $resource = $request->getQuery()->update($anime);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  AnimeDestroyRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function destroy(AnimeDestroyRequest $request, Anime $anime): JsonResponse
    {
        $resource = $request->getQuery()->destroy($anime);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  AnimeRestoreRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function restore(AnimeRestoreRequest $request, Anime $anime): JsonResponse
    {
        $resource = $request->getQuery()->restore($anime);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AnimeForceDeleteRequest  $request
     * @param  Anime  $anime
     * @return JsonResponse
     */
    public function forceDelete(AnimeForceDeleteRequest $request, Anime $anime): JsonResponse
    {
        return $request->getQuery()->forceDelete($anime);
    }
}
