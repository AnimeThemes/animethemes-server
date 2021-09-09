<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\ThemeShowRequest;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\JsonResponse;

/**
 * Class ThemeController.
 */
class ThemeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  ThemeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ThemeIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return ThemeCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return ThemeCollection::performQuery($this->query)->toResponse($request);
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
        $resource = ThemeResource::performQuery($theme, $this->query);

        return $resource->toResponse($request);
    }
}
