<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Billing\Balance;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Collection;

/**
 * Class BalanceMonthToDateSort.
 */
class BalanceMonthToDateSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'month_to_date_balance');
    }

    /**
     * Get sort column.
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
