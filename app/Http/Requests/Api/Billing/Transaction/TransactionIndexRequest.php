<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Query\Billing\TransactionQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class TransactionIndexRequest.
 */
class TransactionIndexRequest extends EloquentIndexRequest
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
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return new TransactionQuery($this->validated());
    }
}
