<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\Balance\BalanceIndexRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceShowRequest;
use App\Models\Billing\Balance;
use Illuminate\Http\JsonResponse;

/**
 * Class BalanceController.
 */
class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  BalanceIndexRequest  $request
     * @return JsonResponse
     */
    public function index(BalanceIndexRequest $request): JsonResponse
    {
        $balances = $request->getQuery()->index();

        return $balances->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  BalanceShowRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    public function show(BalanceShowRequest $request, Balance $balance): JsonResponse
    {
        $resource = $request->getQuery()->show($balance);

        return $resource->toResponse($request);
    }
}
