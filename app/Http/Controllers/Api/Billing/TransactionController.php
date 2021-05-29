<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Billing\TransactionCollection;
use App\Http\Resources\Billing\TransactionResource;
use App\Models\Billing\Transaction;

class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return TransactionCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Transaction $transaction)
    {
        $resource = TransactionResource::performQuery($transaction, $this->parser);

        return $resource->toResponse(request());
    }
}
