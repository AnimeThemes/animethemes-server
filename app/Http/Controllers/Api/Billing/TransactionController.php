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
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TransactionController.
 */
class TransactionController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Transaction::class, 'transaction');
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

        $transactions = $action->index(Transaction::query(), $query, $request->schema());

        $collection = new TransactionCollection($transactions, $query);

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
        $transaction = $action->store(Transaction::query(), $request->validated());

        $resource = new TransactionResource($transaction, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Transaction  $transaction
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Transaction $transaction, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($transaction, $query, $request->schema());

        $resource = new TransactionResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Transaction  $transaction
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Transaction $transaction, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($transaction, $request->validated());

        $resource = new TransactionResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  Transaction  $transaction
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, Transaction $transaction, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($transaction);

        $resource = new TransactionResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  Transaction  $transaction
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, Transaction $transaction, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($transaction);

        $resource = new TransactionResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  Transaction  $transaction
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(Transaction $transaction, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($transaction);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
