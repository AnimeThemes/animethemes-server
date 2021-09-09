<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Billing\Balance\BalanceIndexRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceShowRequest;
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
     * Display a listing of the resource.
     *
     * @param BalanceIndexRequest $request
     * @return JsonResponse
     */
    public function index(BalanceIndexRequest $request): JsonResponse
    {
        return BalanceCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param BalanceShowRequest $request
     * @param Balance $balance
     * @return JsonResponse
     */
    public function show(BalanceShowRequest $request, Balance $balance): JsonResponse
    {
        $resource = BalanceResource::performQuery($balance, $this->query);

        return $resource->toResponse($request);
    }
}
