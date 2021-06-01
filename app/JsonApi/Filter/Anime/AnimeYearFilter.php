<?php declare(strict_types=1);

namespace App\JsonApi\Filter\Anime;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

/**
 * Class AnimeYearFilter
 * @package App\JsonApi\Filter\Anime
 */
class AnimeYearFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'year');
    }
}
