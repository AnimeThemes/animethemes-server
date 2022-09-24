<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing\Balance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\Balance\BalanceDestroyRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceForceDeleteRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceIndexRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceRestoreRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceShowRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceStoreRequest;
use App\Http\Requests\Api\Billing\Balance\BalanceUpdateRequest;
use App\Models\Billing\Balance;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

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
    #[Route(fullUri: 'balance', name: 'balance.index')]
    public function index(BalanceIndexRequest $request): JsonResponse
    {
        $balances = $request->getQuery()->index();

        return $balances->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  BalanceStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'balance', name: 'balance.store', middleware: 'auth:sanctum')]
    public function store(BalanceStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  BalanceShowRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    #[Route(fullUri: 'balance/{balance}', name: 'balance.show')]
    public function show(BalanceShowRequest $request, Balance $balance): JsonResponse
    {
        $resource = $request->getQuery()->show($balance);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  BalanceUpdateRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    #[Route(fullUri: 'balance/{balance}', name: 'balance.update', middleware: 'auth:sanctum')]
    public function update(BalanceUpdateRequest $request, Balance $balance): JsonResponse
    {
        $resource = $request->getQuery()->update($balance);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  BalanceDestroyRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    #[Route(fullUri: 'balance/{balance}', name: 'balance.destroy', middleware: 'auth:sanctum')]
    public function destroy(BalanceDestroyRequest $request, Balance $balance): JsonResponse
    {
        $resource = $request->getQuery()->destroy($balance);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  BalanceRestoreRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    #[Route(method: 'patch', fullUri: 'restore/balance/{balance}', name: 'balance.restore', middleware: 'auth:sanctum')]
    public function restore(BalanceRestoreRequest $request, Balance $balance): JsonResponse
    {
        $resource = $request->getQuery()->restore($balance);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  BalanceForceDeleteRequest  $request
     * @param  Balance  $balance
     * @return JsonResponse
     */
    #[Route(method: 'delete', fullUri: 'forceDelete/balance/{balance}', name: 'balance.forceDelete', middleware: 'auth:sanctum')]
    public function forceDelete(BalanceForceDeleteRequest $request, Balance $balance): JsonResponse
    {
        return $request->getQuery()->forceDelete($balance);
    }
}
