<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Billing\Transaction;

use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TransactionReadQuery.
 */
class TransactionReadQuery extends EloquentReadQuery
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
    public function indexBuilder(): Builder
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
        return new TransactionResource($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return new TransactionCollection($resource, $this);
    }
}
