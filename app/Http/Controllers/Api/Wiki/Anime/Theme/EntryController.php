<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime\Theme;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryDestroyRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryRestoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryShowRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryStoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryUpdateRequest;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\JsonResponse;

/**
 * Class EntryController.
 */
class EntryController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::class, 'animethemeentry');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  EntryIndexRequest  $request
     * @return JsonResponse
     */
    public function index(EntryIndexRequest $request): JsonResponse
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
     * @param  EntryStoreRequest  $request
     * @return JsonResponse
     */
    public function store(EntryStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  EntryShowRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @return JsonResponse
     */
    public function show(EntryShowRequest $request, AnimeThemeEntry $animethemeentry): JsonResponse
    {
        $resource = $request->getQuery()->show($animethemeentry);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  EntryUpdateRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @return JsonResponse
     */
    public function update(EntryUpdateRequest $request, AnimeThemeEntry $animethemeentry): JsonResponse
    {
        $resource = $request->getQuery()->update($animethemeentry);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  EntryDestroyRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @return JsonResponse
     */
    public function destroy(EntryDestroyRequest $request, AnimeThemeEntry $animethemeentry): JsonResponse
    {
        $resource = $request->getQuery()->destroy($animethemeentry);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  EntryRestoreRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @return JsonResponse
     */
    public function restore(EntryRestoreRequest $request, AnimeThemeEntry $animethemeentry): JsonResponse
    {
        $resource = $request->getQuery()->restore($animethemeentry);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  EntryForceDeleteRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @return JsonResponse
     */
    public function forceDelete(EntryForceDeleteRequest $request, AnimeThemeEntry $animethemeentry): JsonResponse
    {
        return $request->getQuery()->forceDelete($animethemeentry);
    }
}
