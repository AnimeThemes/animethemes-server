<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Billing\Collection\BalanceCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class BalanceController.
 */
class BalanceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return BalanceCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Balance $balance
     * @return JsonResponse
     */
    public function show(Request $request, Balance $balance): JsonResponse
    {
        $resource = BalanceResource::performQuery($balance, $this->query);

        return $resource->toResponse($request);
    }
}
