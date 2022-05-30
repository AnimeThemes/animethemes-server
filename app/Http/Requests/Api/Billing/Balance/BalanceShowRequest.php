<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Query\Billing\Balance\BalanceReadQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentShowRequest;

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
    protected function schema(): EloquentSchema
    {
        return new BalanceSchema();
    }

    /**
     * Get the validation API Query.
     *
     * @return EloquentReadQuery
     */
    public function getQuery(): EloquentReadQuery
    {
        return new BalanceReadQuery($this->validated());
    }
}
