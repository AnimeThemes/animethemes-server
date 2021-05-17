<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BalanceCollection;
use App\Http\Resources\BalanceResource;
use App\Models\Balance;

class BalanceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return BalanceCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Balance  $balance
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Balance $balance)
    {
        $resource = BalanceResource::performQuery($balance, $this->parser);

        return $resource->toResponse(request());
    }
}
