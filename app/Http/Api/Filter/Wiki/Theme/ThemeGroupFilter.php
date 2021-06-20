<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Theme;

use App\Http\Api\Filter\Filter;
use App\Http\Api\QueryParser;

/**
 * Class ThemeGroupFilter.
 */
class ThemeGroupFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'group');
    }
}
