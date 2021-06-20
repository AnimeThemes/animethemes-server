<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\QueryParser;

/**
 * Class ThemeTypeFilter.
 */
class ThemeTypeFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'type', ThemeType::class);
    }
}
