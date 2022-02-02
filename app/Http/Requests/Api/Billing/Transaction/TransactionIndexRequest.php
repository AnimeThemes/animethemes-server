<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Transaction;

use App\Http\Api\Query\Billing\TransactionQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Requests\Api\EloquentIndexRequest;

/**
 * Class TransactionIndexRequest.
 */
class TransactionIndexRequest extends EloquentIndexRequest
{
    /**
     * Get the schema.
     *
     * @return Schema
     */
    protected function getSchema(): Schema
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
        return TransactionQuery::make($this->validated());
    }
}
