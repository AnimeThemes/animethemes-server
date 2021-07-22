<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Anime;

use App\Http\Api\Filter\StringFilter;
use App\Http\Api\QueryParser;

/**
 * Class AnimeSlugFilter.
 */
class AnimeSlugFilter extends StringFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'slug');
    }
}