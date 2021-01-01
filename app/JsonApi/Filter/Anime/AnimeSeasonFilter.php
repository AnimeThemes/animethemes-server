<?php

namespace App\JsonApi\Filter\Anime;

use App\Enums\AnimeSeason;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class AnimeSeasonFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'season', AnimeSeason::class);
    }
}
