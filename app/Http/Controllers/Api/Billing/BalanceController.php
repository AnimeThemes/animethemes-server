<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Billing\BalanceCollection;
use App\Http\Resources\Billing\BalanceResource;
use App\Models\Billing\Balance;

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
     * @param  \App\Models\Billing\Balance  $balance
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Balance $balance)
    {
        $resource = BalanceResource::performQuery($balance, $this->parser);

        return $resource->toResponse(request());
    }
}
