<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Transaction;

use App\Enums\Models\Billing\Service;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class TransactionServiceFilter.
 */
class TransactionServiceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'service', Service::class);
    }
}
