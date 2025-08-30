<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Song;

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
use App\Http\Resources\Wiki\Song\Collection\PerformanceCollection;
use App\Http\Resources\Wiki\Song\Resource\PerformanceResource;
use App\Models\Wiki\Song\Performance;
use Illuminate\Http\JsonResponse;

class PerformanceController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Performance::class, 'performance');
    }

    /**
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): PerformanceCollection
    {
        $query = new Query($request->validated());

        $performances = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Performance::query(), $query, $request->schema());

        return new PerformanceCollection($performances, $query);
    }

    /**
     * @param  StoreAction<Performance>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): PerformanceResource
    {
        $performance = $action->store(Performance::query(), $request->validated());

        return new PerformanceResource($performance, new Query());
    }

    /**
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Performance $performance, ShowAction $action): PerformanceResource
    {
        $query = new Query($request->validated());

        $show = $action->show($performance, $query, $request->schema());

        return new PerformanceResource($show, $query);
    }

    /**
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Performance $performance, UpdateAction $action): PerformanceResource
    {
        $updated = $action->update($performance, $request->validated());

        return new PerformanceResource($updated, new Query());
    }

    /**
     * @param  DestroyAction  $action
     */
    public function destroy(Performance $performance, DestroyAction $action): PerformanceResource
    {
        $deleted = $action->destroy($performance);

        return new PerformanceResource($deleted, new Query());
    }

    /**
     * @param  RestoreAction  $action
     */
    public function restore(Performance $performance, RestoreAction $action): PerformanceResource
    {
        $restored = $action->restore($performance);

        return new PerformanceResource($restored, new Query());
    }

    /**
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(Performance $performance, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($performance);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
