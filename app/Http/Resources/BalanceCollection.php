<?php

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceCollectionQuery;
use App\JsonApi\Filter\Balance\BalanceDateFilter;
use App\JsonApi\Filter\Balance\BalanceFrequencyFilter;
use App\JsonApi\Filter\Balance\BalanceServiceFilter;
use App\JsonApi\Filter\Base\CreatedAtFilter;
use App\JsonApi\Filter\Base\DeletedAtFilter;
use App\JsonApi\Filter\Base\TrashedFilter;
use App\JsonApi\Filter\Base\UpdatedAtFilter;

class BalanceCollection extends BaseCollection
{
    use PerformsResourceCollectionQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'balances';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function (BalanceResource $resource) {
            return $resource->parser($this->parser);
        })->all();
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return array
     */
    public static function allowedSortFields()
    {
        return [
            'balance_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'date',
            'service',
            'frequency',
            'usage',
            'month_to_date_balance',
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
            BalanceDateFilter::class,
            BalanceFrequencyFilter::class,
            BalanceServiceFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
