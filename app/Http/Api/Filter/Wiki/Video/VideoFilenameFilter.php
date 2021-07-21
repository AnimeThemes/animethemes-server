<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Video;

use App\Http\Api\Filter\StringFilter;
use App\Http\Api\QueryParser;

/**
 * Class VideoFilenameFilter.
 */
class VideoFilenameFilter extends StringFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'filename');
    }
}
