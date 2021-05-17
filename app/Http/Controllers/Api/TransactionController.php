<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

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
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Transaction $transaction)
    {
        $resource = TransactionResource::performQuery($transaction, $this->parser);

        return $resource->toResponse(request());
    }
}
