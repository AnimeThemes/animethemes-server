<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ThemeController.
 */
class ThemeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return ThemeCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return ThemeCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Theme $theme
     * @return JsonResponse
     */
    public function show(Request $request, Theme $theme): JsonResponse
    {
        $resource = ThemeResource::performQuery($theme, $this->query);

        return $resource->toResponse($request);
    }
}
