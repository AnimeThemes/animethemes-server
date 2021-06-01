<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Video;

use App\Enums\VideoSource;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

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
