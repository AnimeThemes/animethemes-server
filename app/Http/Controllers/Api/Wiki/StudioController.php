<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Resource\StudioResource;
use App\Models\Wiki\Studio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class StudioController.
 */
class StudioController extends BaseController
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
            return StudioCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return StudioCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Studio $studio
     * @return JsonResponse
     */
    public function show(Request $request, Studio $studio): JsonResponse
    {
        $resource = StudioResource::performQuery($studio, $this->query);

        return $resource->toResponse($request);
    }
}
