<?php

namespace App\JsonApi\Filter\Billing\Transaction;

use App\Enums\Billing\Service;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class TransactionServiceFilter extends EnumFilter
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
