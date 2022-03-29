<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Billing\Transaction;

use App\Http\Api\Query\Base\EloquentWriteQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\EloquentSchema;
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
     * Get the resource schema.
     *
     * @return EloquentSchema
     */
    public function schema(): EloquentSchema
    {
        return new TransactionSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder
     */
    public function builder(): Builder
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
        return TransactionResource::make($resource, new TransactionReadQuery());
    }
}
