<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Base;

use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;

/**
 * Class UpdatedAtFilter
 * @package App\JsonApi\Filter\Base
 */
class UpdatedAtFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'updated_at');
    }
}
