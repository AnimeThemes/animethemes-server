<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return BalanceCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Balance $balance
     * @return JsonResponse
     */
    public function show(Balance $balance): JsonResponse
    {
        $resource = BalanceResource::performQuery($balance, $this->parser);

        return $resource->toResponse(request());
    }
}
