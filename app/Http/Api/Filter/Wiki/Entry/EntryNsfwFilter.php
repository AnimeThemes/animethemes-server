<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Entry;

use App\Http\Api\Filter\BooleanFilter;
use Illuminate\Support\Collection;

/**
 * Class EntryNsfwFilter.
 */
class EntryNsfwFilter extends BooleanFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'nsfw');
    }
}
