<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Entry;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

/**
 * Class EntryVersionFilter
 * @package App\JsonApi\Filter\Entry
 */
class EntryVersionFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'version');
    }
}
