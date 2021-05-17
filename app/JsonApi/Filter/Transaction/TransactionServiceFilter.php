<?php

namespace App\JsonApi\Filter\Transaction;

use App\Enums\BillingService;
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
        parent::__construct($parser, 'service', BillingService::class);
    }
}
