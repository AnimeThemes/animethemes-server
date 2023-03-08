<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeSeriesCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeSeriesController.
 */
class AnimeSeriesController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime', Series::class, 'series');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $resources = $action->index(AnimeSeries::query(), $query, $request->schema());

        $collection = new AnimeSeriesCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreAction $action): JsonResponse
    {
        $animeSeries = $action->store(AnimeSeries::query(), $request->validated());

        $resource = new AnimeSeriesResource($animeSeries, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  Series  $series
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Anime $anime, Series $series, ShowAction $action): JsonResponse
    {
        $animeSeries = AnimeSeries::query()
            ->where(AnimeSeries::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeSeries::ATTRIBUTE_SERIES, $series->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeSeries, $query, $request->schema());

        $resource = new AnimeSeriesResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Anime  $anime
     * @param  Series  $series
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Anime $anime, Series $series, DestroyAction $action): JsonResponse
    {
        $animeSeries = AnimeSeries::query()
            ->where(AnimeSeries::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeSeries::ATTRIBUTE_SERIES, $series->getKey())
            ->firstOrFail();

        $action->destroy($animeSeries);

        return new JsonResponse([
            'message' => "Anime '{$series->getName()}' has been detached from Series '{$anime->getName()}'.",
        ]);
    }
}
