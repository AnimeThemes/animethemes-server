<?php

namespace App\JsonApi\Filter\Anime;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

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