<?php

namespace App\JsonApi\Filter\Video;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

class VideoResolutionFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'resolution');
    }
}
