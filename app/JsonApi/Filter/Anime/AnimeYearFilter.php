<?php

namespace App\JsonApi\Filter\Anime;

use App\JsonApi\QueryParser;
use App\JsonApi\Filter\Filter;

class AnimeYearFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'year');
    }
}
