<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Transaction;

use App\Http\Api\Filter\DateFilter;
use Illuminate\Support\Collection;

/**
 * Class TransactionDateFilter.
 */
class TransactionDateFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'date');
    }
}
