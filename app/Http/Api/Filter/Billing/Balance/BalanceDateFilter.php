<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Balance;

use App\Http\Api\Filter\DateFilter;
use App\Http\Api\QueryParser;

/**
 * Class BalanceDateFilter.
 */
class BalanceDateFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'date');
    }
}
