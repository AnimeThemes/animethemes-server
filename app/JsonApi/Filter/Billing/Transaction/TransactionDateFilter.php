<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Billing\Transaction;

use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;

/**
 * Class TransactionDateFilter
 * @package App\JsonApi\Filter\Billing\Transaction
 */
class TransactionDateFilter extends DateFilter
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
