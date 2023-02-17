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
use Illuminate\Http\Request;

/**
 * Class SeriesController.
 */
class SeriesController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Series::class, 'series');
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

        $series = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Series::query(), $query, $request->schema());

        $collection = new SeriesCollection($series, $query);

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
        $series = $action->store(Series::query(), $request->validated());

        $resource = new SeriesResource($series, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Series  $series
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Series $series, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($series, $query, $request->schema());

        $resource = new SeriesResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Series  $series
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Series $series, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($series, $request->validated());

        $resource = new SeriesResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Series  $series
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Series $series, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($series);

        $resource = new SeriesResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Series  $series
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Series $series, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($series);

        $resource = new SeriesResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Series  $series
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Series $series, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($series);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
