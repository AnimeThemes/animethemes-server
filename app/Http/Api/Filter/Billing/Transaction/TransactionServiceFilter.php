<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Billing\Transaction;

use App\Enums\Billing\Service;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class TransactionServiceFilter.
 */
class TransactionServiceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'service', Service::class);
    }
}
