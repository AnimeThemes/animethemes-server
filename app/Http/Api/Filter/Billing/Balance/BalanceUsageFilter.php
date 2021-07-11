<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\QueryParser;

/**
 * Class BalanceServiceFilter.
 */
class BalanceUsageFilter extends FloatFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'usage');
    }
}
