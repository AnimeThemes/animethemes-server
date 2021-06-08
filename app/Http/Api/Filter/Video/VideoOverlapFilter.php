<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Video;

use App\Enums\VideoOverlap;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class VideoOverlapFilter.
 */
class VideoOverlapFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'overlap', VideoOverlap::class);
    }
}
