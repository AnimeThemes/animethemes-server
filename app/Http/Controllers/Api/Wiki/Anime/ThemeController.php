<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
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
class ThemeController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::class, 'animetheme');
    }

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
     * @param  AnimeTheme  $animetheme
     * @return JsonResponse
     */
    public function show(ThemeShowRequest $request, AnimeTheme $animetheme): JsonResponse
    {
        $resource = $request->getQuery()->show($animetheme);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  ThemeUpdateRequest  $request
     * @param  AnimeTheme  $animetheme
     * @return JsonResponse
     */
    public function update(ThemeUpdateRequest $request, AnimeTheme $animetheme): JsonResponse
    {
        $resource = $request->getQuery()->update($animetheme);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  ThemeDestroyRequest  $request
     * @param  AnimeTheme  $animetheme
     * @return JsonResponse
     */
    public function destroy(ThemeDestroyRequest $request, AnimeTheme $animetheme): JsonResponse
    {
        $resource = $request->getQuery()->destroy($animetheme);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  ThemeRestoreRequest  $request
     * @param  AnimeTheme  $animetheme
     * @return JsonResponse
     */
    public function restore(ThemeRestoreRequest $request, AnimeTheme $animetheme): JsonResponse
    {
        $resource = $request->getQuery()->restore($animetheme);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ThemeForceDeleteRequest  $request
     * @param  AnimeTheme  $animetheme
     * @return JsonResponse
     */
    public function forceDelete(ThemeForceDeleteRequest $request, AnimeTheme $animetheme): JsonResponse
    {
        return $request->getQuery()->forceDelete($animetheme);
    }
}
