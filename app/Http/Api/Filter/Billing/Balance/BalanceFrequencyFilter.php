<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class BalanceFrequencyFilter.
 */
class BalanceFrequencyFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'frequency', BalanceFrequency::class);
    }
}
