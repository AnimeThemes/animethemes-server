<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeShowRequest;
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
}
