<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Billing\TransactionCollection;
use App\Http\Resources\Billing\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\JsonResponse;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Api\Billing
 */
class TransactionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return TransactionCollection::performQuery($this->parser)->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $resource = TransactionResource::performQuery($transaction, $this->parser);

        return $resource->toResponse(request());
    }
}
