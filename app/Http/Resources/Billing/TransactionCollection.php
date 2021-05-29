<?php

namespace App\Http\Resources\Billing;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\Http\Resources\BaseCollection;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;
use App\JsonApi\Filter\Billing\Transaction\TransactionDateFilter;
use App\JsonApi\Filter\Billing\Transaction\TransactionServiceFilter;
use App\Models\Billing\Transaction;

class TransactionCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'transactions';

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (TransactionResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * Resolve the model query builder from collection class name.
     * We are assuming a convention of "{Model}Collection".
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function queryBuilder()
    {
        return Transaction::query();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields()
    {
        return [
            'transaction_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'date',
            'service',
            'description',
            'amount',
            'external_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return array
     */
    public static function filters()
    {
        return [
            TransactionDateFilter::class,
            TransactionServiceFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
