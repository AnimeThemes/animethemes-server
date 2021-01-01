<?php

namespace App\JsonApi\Filter\Entry;

use App\JsonApi\QueryParser;
use App\JsonApi\Filter\Filter;

class EntryVersionFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'version');
    }
}