<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Resource\ThemeResource;
use App\Models\Wiki\Theme;
use Illuminate\Http\JsonResponse;

/**
 * Class ThemeController.
 */
class ThemeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return ThemeCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse(request());
        }

        return ThemeCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Theme $theme
     * @return JsonResponse
     */
    public function show(Theme $theme): JsonResponse
    {
        $resource = ThemeResource::performQuery($theme, $this->parser);

        return $resource->toResponse(request());
    }
}
