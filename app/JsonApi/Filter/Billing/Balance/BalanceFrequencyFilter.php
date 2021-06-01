<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Billing\Balance;

use App\Enums\Billing\Frequency;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class BalanceFrequencyFilter.
 */
class BalanceFrequencyFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'frequency', Frequency::class);
    }
}
