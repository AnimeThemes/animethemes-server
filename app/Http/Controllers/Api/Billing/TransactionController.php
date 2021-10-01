<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\Transaction\TransactionIndexRequest;
use App\Http\Requests\Api\Billing\Transaction\TransactionShowRequest;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController.
 */
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  TransactionIndexRequest  $request
     * @return JsonResponse
     */
    public function index(TransactionIndexRequest $request): JsonResponse
    {
        return TransactionCollection::performQuery($request->getQuery())->toResponse($request);
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
        $resource = TransactionResource::performQuery($transaction, $request->getQuery());

        return $resource->toResponse($request);
    }
}
