<?php

namespace App\JsonApi\Filter\Entry;

use App\JsonApi\Filter\BooleanFilter;
use App\JsonApi\QueryParser;

class EntrySpoilerFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'spoiler');
    }
}
