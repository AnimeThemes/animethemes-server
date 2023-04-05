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
     * @return BalanceCollection
     */
    public function index(IndexRequest $request, IndexAction $action): BalanceCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(Balance::query(), $query, $request->schema());

        return new BalanceCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return BalanceResource
     */
    public function store(StoreRequest $request, StoreAction $action): BalanceResource
    {
        $balance = $action->store(Balance::query(), $request->validated());

        return new BalanceResource($balance, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Balance  $balance
     * @param  ShowAction  $action
     * @return BalanceResource
     */
    public function show(ShowRequest $request, Balance $balance, ShowAction $action): BalanceResource
    {
        $query = new Query($request->validated());

        $show = $action->show($balance, $query, $request->schema());

        return new BalanceResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Balance  $balance
     * @param  UpdateAction  $action
     * @return BalanceResource
     */
    public function update(UpdateRequest $request, Balance $balance, UpdateAction $action): BalanceResource
    {
        $updated = $action->update($balance, $request->validated());

        return new BalanceResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Balance  $balance
     * @param  DestroyAction  $action
     * @return BalanceResource
     */
    public function destroy(Balance $balance, DestroyAction $action): BalanceResource
    {
        $deleted = $action->destroy($balance);

        return new BalanceResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Balance  $balance
     * @param  RestoreAction  $action
     * @return BalanceResource
     */
    public function restore(Balance $balance, RestoreAction $action): BalanceResource
    {
        $restored = $action->restore($balance);

        return new BalanceResource($restored, new Query());
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
