<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing\Transaction;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Billing\Transaction\TransactionDestroyRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionForceDeleteRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionIndexRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionRestoreRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionShowRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionStoreRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionUpdateRequest;
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
     * @param  TransactionIndexRequest  $request
     * @return JsonResponse
     */
    public function index(TransactionIndexRequest $request): JsonResponse
    {
        $transactions = $request->getQuery()->index();

        return $transactions->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  TransactionStoreRequest  $request
     * @return JsonResponse
     */
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  TransactionShowRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function show(TransactionShowRequest $request, Transaction $transaction): JsonResponse
    {
        $resource = $request->getQuery()->show($transaction);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  TransactionUpdateRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function update(TransactionUpdateRequest $request, Transaction $transaction): JsonResponse
    {
        $resource = $request->getQuery()->update($transaction);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  TransactionDestroyRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function destroy(TransactionDestroyRequest $request, Transaction $transaction): JsonResponse
    {
        $resource = $request->getQuery()->destroy($transaction);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  TransactionRestoreRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function restore(TransactionRestoreRequest $request, Transaction $transaction): JsonResponse
    {
        $resource = $request->getQuery()->restore($transaction);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  TransactionForceDeleteRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function forceDelete(TransactionForceDeleteRequest $request, Transaction $transaction): JsonResponse
    {
        return $request->getQuery()->forceDelete($transaction);
    }
}
