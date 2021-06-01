<?php

declare(strict_types=1);

namespace App\JsonApi\Filter\Theme;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;

/**
 * Class ThemeSequenceFilter
 * @package App\JsonApi\Filter\Theme
 */
class ThemeSequenceFilter extends Filter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'sequence');
    }
}
