<?php

declare(strict_types=1);

namespace App\Http\Api\Query\Billing;

use App\Http\Api\Query\EloquentQuery;
use App\Http\Api\Schema\Billing\BalanceSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Collection\BalanceCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BalanceQuery.
 */
class BalanceQuery extends EloquentQuery
{
    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new BalanceSchema();
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder|null
     */
    public function builder(): ?Builder
    {
        return Balance::query();
    }

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    public function resource(mixed $resource): BaseResource
    {
        return BalanceResource::make($resource, $this);
    }

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    public function collection(mixed $resource): BaseCollection
    {
        return BalanceCollection::make($resource, $this);
    }
}
