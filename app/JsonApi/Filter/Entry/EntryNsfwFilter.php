<?php declare(strict_types=1);

namespace App\JsonApi\Filter\Entry;

use App\JsonApi\Filter\BooleanFilter;
use App\JsonApi\QueryParser;

/**
 * Class EntryNsfwFilter
 * @package App\JsonApi\Filter\Entry
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
