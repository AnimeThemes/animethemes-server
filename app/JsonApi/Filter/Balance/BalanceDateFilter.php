<?php

namespace App\JsonApi\Filter\Balance;

use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;

class BalanceDateFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'date');
    }
}
