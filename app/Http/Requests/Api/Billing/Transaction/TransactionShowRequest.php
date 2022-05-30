<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Billing\Transaction\TransactionReadQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

/**
 * Class TransactionShowRequest.
 */
class TransactionShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function schema(): EloquentSchema
    {
        return new TransactionSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new TransactionReadQuery($this->validated());
    }
}
