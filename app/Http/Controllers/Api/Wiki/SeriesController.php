<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Series\SeriesIndexRequest;
use App\Http\Requests\Api\Wiki\Series\SeriesShowRequest;
use App\Models\Wiki\Series;
use Illuminate\Http\JsonResponse;

/**
 * Class SeriesController.
 */
class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  SeriesIndexRequest  $request
     * @return JsonResponse
     */
    public function index(SeriesIndexRequest $request): JsonResponse
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
     * @param  SeriesShowRequest  $request
     * @param  Series  $series
     * @return JsonResponse
     */
    public function show(SeriesShowRequest $request, Series $series): JsonResponse
    {
        $resource = $request->getQuery()->show($series);

        return $resource->toResponse($request);
    }
}
