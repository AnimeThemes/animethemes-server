<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Video;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

/**
 * Class VideoResolutionFilter.
 */
class VideoResolutionFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'resolution');
    }
}
