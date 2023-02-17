<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

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
use App\Http\Resources\Billing\Collection\BalanceCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class BalanceController.
 */
class BalanceController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Balance::class, 'balance');
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

        $resources = $action->index(Balance::query(), $query, $request->schema());

        $collection = new BalanceCollection($resources, $query);

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
        $balance = $action->store(Balance::query(), $request->validated());

        $resource = new BalanceResource($balance, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Balance  $balance
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Balance $balance, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($balance, $query, $request->schema());

        $resource = new BalanceResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Balance  $balance
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Balance $balance, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($balance, $request->validated());

        $resource = new BalanceResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Balance  $balance
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Balance $balance, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($balance);

        $resource = new BalanceResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Balance  $balance
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Balance $balance, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($balance);

        $resource = new BalanceResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Balance  $balance
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Balance $balance, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($balance);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
