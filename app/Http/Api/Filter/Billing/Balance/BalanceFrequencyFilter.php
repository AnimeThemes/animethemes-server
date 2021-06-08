<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Enums\Billing\Frequency;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class BalanceFrequencyFilter.
 */
class BalanceFrequencyFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'frequency', Frequency::class);
    }
}
