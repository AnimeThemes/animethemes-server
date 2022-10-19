<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Billing\Transaction;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TransactionWriteQuery.
 */
class TransactionWriteQuery extends EloquentWriteQuery
{
    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return Transaction::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return new TransactionResource($resource, new TransactionReadQuery());
    }
}
