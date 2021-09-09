<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\FloatFilter;
use Illuminate\Support\Collection;

/**
 * Class BalanceMonthToDateFilter.
 */
class BalanceMonthToDateFilter extends FloatFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'month_to_date_balance');
    }

    /**
     * Get filter column.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getColumn(): string
    {
        return 'balance';
    }
}
