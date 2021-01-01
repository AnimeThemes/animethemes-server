<?php

namespace App\JsonApi\Filter\Video;

use App\Enums\VideoOverlap;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class VideoOverlapFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'overlap', VideoOverlap::class);
    }
}
