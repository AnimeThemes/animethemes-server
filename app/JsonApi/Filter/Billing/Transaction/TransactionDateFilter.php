<?php

namespace App\JsonApi\Filter\Billing\Transaction;

use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;

class TransactionDateFilter extends DateFilter
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
