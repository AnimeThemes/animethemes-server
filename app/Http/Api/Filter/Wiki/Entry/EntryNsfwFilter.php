<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Entry;

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\QueryParser;

/**
 * Class EntryNsfwFilter.
 */
class EntryNsfwFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'nsfw');
    }
}
