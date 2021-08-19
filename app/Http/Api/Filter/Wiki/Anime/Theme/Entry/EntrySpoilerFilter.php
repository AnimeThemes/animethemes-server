<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Anime\Theme\Entry;

use App\Http\Api\Filter\BooleanFilter;
use Illuminate\Support\Collection;

/**
 * Class EntrySpoilerFilter.
 */
class EntrySpoilerFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'spoiler');
    }
}