<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Series\SeriesIndexRequest;
use App\Http\Requests\Api\Wiki\Series\SeriesShowRequest;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Http\JsonResponse;

/**
 * Class SeriesController.
 */
class SeriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param SeriesIndexRequest $request
     * @return JsonResponse
     */
    public function index(SeriesIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return SeriesCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SeriesCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param SeriesShowRequest $request
     * @param Series $series
     * @return JsonResponse
     */
    public function show(SeriesShowRequest $request, Series $series): JsonResponse
    {
        $resource = SeriesResource::performQuery($series, $this->query);

        return $resource->toResponse($request);
    }
}
