<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Billing\Balance;

use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;

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
