<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Video;

use App\Http\Api\Filter\Filter;
use App\Http\Api\QueryParser;

/**
 * Class VideoBasenameFilter.
 */
class VideoBasenameFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'basename');
    }
}
