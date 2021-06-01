<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Video;

use App\JsonApi\Filter\BooleanFilter;
use App\JsonApi\QueryParser;

/**
 * Class VideoSubbedFilter
 * @package App\JsonApi\Filter\Video
 */
class VideoSubbedFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'subbed');
    }
}
