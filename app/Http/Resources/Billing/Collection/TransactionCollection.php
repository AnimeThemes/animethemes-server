<?php

declare(strict_types=1);

namespace App\Http\Resources\Billing\Collection;

use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria;
use App\Http\Api\Filter\Billing\Transaction\TransactionAmountFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionDateFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionDescriptionFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionExternalIdFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionIdFilter;
use App\Http\Api\Filter\Billing\Transaction\TransactionServiceFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Sort\Billing\Transaction\TransactionAmountSort;
use App\Http\Api\Sort\Billing\Transaction\TransactionDateSort;
use App\Http\Api\Sort\Billing\Transaction\TransactionDescriptionSort;
use App\Http\Api\Sort\Billing\Transaction\TransactionExternalIdSort;
use App\Http\Api\Sort\Billing\Transaction\TransactionIdSort;
use App\Http\Api\Sort\Billing\Transaction\TransactionServiceSort;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class TransactionCollection.
 */
class TransactionCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'transactions';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = Transaction::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (Transaction $transaction) {
            return TransactionResource::make($transaction, $this->query);
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
     * @param  Collection<Criteria>  $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return array_merge(
            parent::sorts($sortCriteria),
            [
                new TransactionIdSort($sortCriteria),
                new TransactionDateSort($sortCriteria),
                new TransactionServiceSort($sortCriteria),
                new TransactionDescriptionSort($sortCriteria),
                new TransactionAmountSort($sortCriteria),
                new TransactionExternalIdSort($sortCriteria),
            ]
        );
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param  Collection<FilterCriteria>  $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return array_merge(
            parent::filters($filterCriteria),
            [
                new TransactionIdFilter($filterCriteria),
                new TransactionDateFilter($filterCriteria),
                new TransactionServiceFilter($filterCriteria),
                new TransactionDescriptionFilter($filterCriteria),
                new TransactionAmountFilter($filterCriteria),
                new TransactionExternalIdFilter($filterCriteria),
            ]
        );
    }
}
