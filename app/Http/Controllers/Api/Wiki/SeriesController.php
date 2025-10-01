<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;
use Illuminate\Http\JsonResponse;

class SeriesController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Series::class, 'series');
    }

    public function index(IndexRequest $request, IndexAction $action): SeriesCollection
    {
        $query = new Query($request->validated());

        $series = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Series::query(), $query, $request->schema());

        return new SeriesCollection($series, $query);
    }

    /**
     * @param  StoreAction<Series>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): SeriesResource
    {
        $series = $action->store(Series::query(), $request->validated());

        return new SeriesResource($series, new Query());
    }

    public function show(ShowRequest $request, Series $series, ShowAction $action): SeriesResource
    {
        $query = new Query($request->validated());

        $show = $action->show($series, $query, $request->schema());

        return new SeriesResource($show, $query);
    }

    public function update(UpdateRequest $request, Series $series, UpdateAction $action): SeriesResource
    {
        $updated = $action->update($series, $request->validated());

        return new SeriesResource($updated, new Query());
    }

    public function destroy(Series $series, DestroyAction $action): SeriesResource
    {
        $deleted = $action->destroy($series);

        return new SeriesResource($deleted, new Query());
    }

    public function restore(Series $series, RestoreAction $action): SeriesResource
    {
        $restored = $action->restore($series);

        return new SeriesResource($restored, new Query());
    }

    public function forceDelete(Series $series, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($series);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
