<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Transaction;

use App\Http\Api\Filter\FloatFilter;
use Illuminate\Support\Collection;

/**
 * Class TransactionAmountFilter.
 */
class TransactionAmountFilter extends FloatFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'amount');
    }
}
