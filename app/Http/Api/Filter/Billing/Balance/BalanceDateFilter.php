<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\DateFilter;
use Illuminate\Support\Collection;

/**
 * Class BalanceDateFilter.
 */
class BalanceDateFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'date');
    }
}
