<?php

namespace App\JsonApi\Filter\Billing\Balance;

use App\Enums\Billing\Service;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class BalanceServiceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'service', Service::class);
    }
}
