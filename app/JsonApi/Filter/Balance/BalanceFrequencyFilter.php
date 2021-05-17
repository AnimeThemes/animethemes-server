<?php

namespace App\JsonApi\Filter\Balance;

use App\Enums\BillingFrequency;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class BalanceFrequencyFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'frequency', BillingFrequency::class);
    }
}
