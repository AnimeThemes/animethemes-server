<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class ThemeTypeFilter.
 */
class ThemeTypeFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'type', ThemeType::class);
    }
}
