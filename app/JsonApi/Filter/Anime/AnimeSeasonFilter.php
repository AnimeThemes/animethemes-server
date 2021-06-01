<?php declare(strict_types=1);

namespace App\JsonApi\Filter\Anime;

use App\Enums\AnimeSeason;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class AnimeSeasonFilter
 * @package App\JsonApi\Filter\Anime
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
