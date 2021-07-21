<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SeriesController.
 */
class SeriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return SeriesCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SeriesCollection::performQuery($this->parser)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Series $series
     * @return JsonResponse
     */
    public function show(Request $request, Series $series): JsonResponse
    {
        $resource = SeriesResource::performQuery($series, $this->parser);

        return $resource->toResponse($request);
    }
}
