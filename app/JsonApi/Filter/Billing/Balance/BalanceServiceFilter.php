<?php declare(strict_types=1);

namespace App\JsonApi\Filter\Billing\Balance;

use App\Enums\Billing\Service;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class BalanceServiceFilter
 * @package App\JsonApi\Filter\Billing\Balance
 */
class BalanceServiceFilter extends EnumFilter
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
