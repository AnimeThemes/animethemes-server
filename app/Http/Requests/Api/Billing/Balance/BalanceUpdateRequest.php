<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Billing\Balance;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Query\Billing\Balance\BalanceWriteQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Requests\Api\Base\EloquentUpdateRequest;

/**
 * Class BalanceUpdateRequest.
 */
class BalanceUpdateRequest extends EloquentUpdateRequest
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
     * @return EloquentWriteQuery
     */
    public function getQuery(): EloquentWriteQuery
    {
        return new BalanceWriteQuery($this->validated());
    }
}
