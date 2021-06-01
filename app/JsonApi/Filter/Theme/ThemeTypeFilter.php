<?php declare(strict_types=1);

namespace App\JsonApi\Filter\Theme;

use App\Enums\ThemeType;
use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;

/**
 * Class ThemeTypeFilter
 * @package App\JsonApi\Filter\Theme
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
