<?php

namespace App\JsonApi\Filter\Theme;

use App\Enums\ThemeType;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

class ThemeTypeFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'type', ThemeType::class);
    }
}
