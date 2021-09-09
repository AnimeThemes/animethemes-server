<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Wiki\Anime\Theme;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Collection;

/**
 * Class ThemeSlugSort.
 */
class ThemeSlugSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'slug');
    }
}
