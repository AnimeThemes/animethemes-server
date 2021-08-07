<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\IntFilter;
use App\Models\Billing\Balance;
use Illuminate\Support\Collection;

/**
 * Class BalanceIdFilter.
 */
class BalanceIdFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'id');
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
        return (new Balance())->getKeyName();
    }
}
