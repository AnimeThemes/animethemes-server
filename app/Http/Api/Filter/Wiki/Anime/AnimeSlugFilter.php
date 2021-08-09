<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Anime;

use App\Http\Api\Filter\StringFilter;
use Illuminate\Support\Collection;

/**
 * Class AnimeSlugFilter.
 */
class AnimeSlugFilter extends StringFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'slug');
    }
}
