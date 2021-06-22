<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Video;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\QueryParser;

/**
 * Class VideoResolutionFilter.
 */
class VideoResolutionFilter extends IntFilter
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
