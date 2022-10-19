<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Billing\Transaction\TransactionWriteQuery;
use App\Http\Requests\Api\Base\EloquentDestroyRequest;

/**
 * Class TransactionDestroyRequest.
 */
class TransactionDestroyRequest extends EloquentDestroyRequest
{
    /**
     * Get the validation API Query.
     *
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new TransactionWriteQuery($this->validated());
    }
}
