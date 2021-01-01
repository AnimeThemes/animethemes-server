<?php

namespace App\JsonApi\Filter\Video;

use App\JsonApi\QueryParser;
use App\JsonApi\Filter\BooleanFilter;

class VideoUncenFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'uncen');
    }
}
