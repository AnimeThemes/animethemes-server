<?php

namespace App\JsonApi\Filter\Entry;

use App\JsonApi\Filter\BooleanFilter;
use App\JsonApi\QueryParser;

class EntryNsfwFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'nsfw');
    }
}
