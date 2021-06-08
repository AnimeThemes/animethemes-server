<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Video;

use App\Enums\VideoSource;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class VideoSourceFilter.
 */
class VideoSourceFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'source', VideoSource::class);
    }
}
