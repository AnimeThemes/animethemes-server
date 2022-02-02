<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Billing;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Billing\TransactionSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Collection\TransactionCollection;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TransactionQuery.
 */
class TransactionQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public static function schema(): Schema
    {
        return new TransactionSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder|null
     */
    public function builder(): ?Builder
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
        return TransactionResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return TransactionCollection::make($resource, $this);
    }
}
