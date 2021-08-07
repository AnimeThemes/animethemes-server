<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Enums\Models\Billing\Service;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class BalanceServiceFilter.
 */
class BalanceServiceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'service', Service::class);
    }
}
