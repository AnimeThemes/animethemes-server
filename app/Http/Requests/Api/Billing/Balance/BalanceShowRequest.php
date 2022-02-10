<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Query\Billing\BalanceQuery;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\EloquentShowRequest;

/**
 * Class BalanceShowRequest.
 */
class BalanceShowRequest extends EloquentShowRequest
{
    /**
     * Get the schema.
     *
     * @return EloquentSchema
     */
    protected function getSchema(): EloquentSchema
    {
        return new BalanceSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentQuery
     */
    public function getQuery(): EloquentQuery
    {
        return BalanceQuery::make($this->validated());
    }
}
