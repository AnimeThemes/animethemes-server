<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\QueryParser;

/**
 * Class BalanceMonthToDateFilter.
 */
class BalanceMonthToDateFilter extends FloatFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'month_to_date_balance');
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
