<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Billing\Balance\BalanceDateFilter;
use App\Http\Api\Filter\Billing\Balance\BalanceFrequencyFilter;
use App\Http\Api\Filter\Billing\Balance\BalanceIdFilter;
use App\Http\Api\Filter\Billing\Balance\BalanceMonthToDateFilter;
use App\Http\Api\Filter\Billing\Balance\BalanceServiceFilter;
use App\Http\Api\Filter\Billing\Balance\BalanceUsageFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Billing\Balance\BalanceDateSort;
use App\Http\Api\Sort\Billing\Balance\BalanceFrequencySort;
use App\Http\Api\Sort\Billing\Balance\BalanceIdSort;
use App\Http\Api\Sort\Billing\Balance\BalanceMonthToDateSort;
use App\Http\Api\Sort\Billing\Balance\BalanceServiceSort;
use App\Http\Api\Sort\Billing\Balance\BalanceUsageSort;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class BalanceCollection.
 */
class BalanceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'balances';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Balance::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Balance $balance) {
            return BalanceResource::make($balance, $this->query);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [];
    }

    /**
     * The sorts that can be applied by the client for this resource.
     *
     * @param Collection<Criteria> $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return array_merge(
            parent::sorts($sortCriteria),
            [
                new BalanceIdSort($sortCriteria),
                new BalanceDateSort($sortCriteria),
                new BalanceServiceSort($sortCriteria),
                new BalanceFrequencySort($sortCriteria),
                new BalanceUsageSort($sortCriteria),
                new BalanceMonthToDateSort($sortCriteria),
            ]
        );
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param Collection<FilterCriteria> $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return array_merge(
            parent::filters($filterCriteria),
            [
                new BalanceIdFilter($filterCriteria),
                new BalanceDateFilter($filterCriteria),
                new BalanceServiceFilter($filterCriteria),
                new BalanceFrequencyFilter($filterCriteria),
                new BalanceUsageFilter($filterCriteria),
                new BalanceMonthToDateFilter($filterCriteria),
            ]
        );
    }
}
