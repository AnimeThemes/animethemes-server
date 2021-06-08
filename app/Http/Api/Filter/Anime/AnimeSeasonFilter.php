<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Anime;

use App\Enums\AnimeSeason;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class AnimeSeasonFilter.
 */
class AnimeSeasonFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'season', AnimeSeason::class);
    }
}
