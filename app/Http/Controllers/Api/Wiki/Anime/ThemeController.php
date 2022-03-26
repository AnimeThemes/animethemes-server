<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeDestroyRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeRestoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeShowRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeStoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeUpdateRequest;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\JsonResponse;

/**
 * Class ThemeController.
 */
class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ThemeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ThemeIndexRequest $request): JsonResponse
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
     * @param  ThemeStoreRequest  $request
     * @return JsonResponse
     */
    public function store(ThemeStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ThemeShowRequest  $request
     * @param  AnimeTheme  $theme
     * @return JsonResponse
     */
    public function show(ThemeShowRequest $request, AnimeTheme $theme): JsonResponse
    {
        $resource = $request->getQuery()->show($theme);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ThemeUpdateRequest  $request
     * @param  AnimeTheme  $theme
     * @return JsonResponse
     */
    public function update(ThemeUpdateRequest $request, AnimeTheme $theme): JsonResponse
    {
        $resource = $request->getQuery()->update($theme);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ThemeDestroyRequest  $request
     * @param  AnimeTheme  $theme
     * @return JsonResponse
     */
    public function destroy(ThemeDestroyRequest $request, AnimeTheme $theme): JsonResponse
    {
        $resource = $request->getQuery()->destroy($theme);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ThemeRestoreRequest  $request
     * @param  AnimeTheme  $theme
     * @return JsonResponse
     */
    public function restore(ThemeRestoreRequest $request, AnimeTheme $theme): JsonResponse
    {
        $resource = $request->getQuery()->restore($theme);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ThemeForceDeleteRequest  $request
     * @param  AnimeTheme  $theme
     * @return JsonResponse
     */
    public function forceDelete(ThemeForceDeleteRequest $request, AnimeTheme $theme): JsonResponse
    {
        return $request->getQuery()->forceDelete($theme);
    }
}
