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
     * @return TransactionCollection
     */
    public function index(IndexRequest $request, IndexAction $action): TransactionCollection
    {
        $query = new Query($request->validated());

        $transactions = $action->index(Transaction::query(), $query, $request->schema());

        return new TransactionCollection($transactions, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return TransactionResource
     */
    public function store(StoreRequest $request, StoreAction $action): TransactionResource
    {
        $transaction = $action->store(Transaction::query(), $request->validated());

        return new TransactionResource($transaction, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Transaction  $transaction
     * @param  ShowAction  $action
     * @return TransactionResource
     */
    public function show(ShowRequest $request, Transaction $transaction, ShowAction $action): TransactionResource
    {
        $query = new Query($request->validated());

        $show = $action->show($transaction, $query, $request->schema());

        return new TransactionResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Transaction  $transaction
     * @param  UpdateAction  $action
     * @return TransactionResource
     */
    public function update(UpdateRequest $request, Transaction $transaction, UpdateAction $action): TransactionResource
    {
        $updated = $action->update($transaction, $request->validated());

        return new TransactionResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  Transaction  $transaction
     * @param  DestroyAction  $action
     * @return TransactionResource
     */
    public function destroy(Transaction $transaction, DestroyAction $action): TransactionResource
    {
        $deleted = $action->destroy($transaction);

        return new TransactionResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  Transaction  $transaction
     * @param  RestoreAction  $action
     * @return TransactionResource
     */
    public function restore(Transaction $transaction, RestoreAction $action): TransactionResource
    {
        $restored = $action->restore($transaction);

        return new TransactionResource($restored, new Query());
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
