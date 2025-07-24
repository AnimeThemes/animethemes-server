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
     * @return AnimeSeriesCollection
     */
    public function index(IndexRequest $request, IndexAction $action): AnimeSeriesCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(AnimeSeries::query(), $query, $request->schema());

        return new AnimeSeriesCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Anime  $anime
     * @param  Series  $series
     * @param  StoreAction<AnimeSeries>  $action
     * @return AnimeSeriesResource
     */
    public function store(StoreRequest $request, Anime $anime, Series $series, StoreAction $action): AnimeSeriesResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                AnimeSeries::ATTRIBUTE_ANIME => $anime->getKey(),
                AnimeSeries::ATTRIBUTE_SERIES => $series->getKey(),
            ]
        );

        $animeSeries = $action->store(AnimeSeries::query(), $validated);

        return new AnimeSeriesResource($animeSeries, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  Series  $series
     * @param  ShowAction  $action
     * @return AnimeSeriesResource
     */
    public function show(ShowRequest $request, Anime $anime, Series $series, ShowAction $action): AnimeSeriesResource
    {
        $animeSeries = AnimeSeries::query()
            ->where(AnimeSeries::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeSeries::ATTRIBUTE_SERIES, $series->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeSeries, $query, $request->schema());

        return new AnimeSeriesResource($show, $query);
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
