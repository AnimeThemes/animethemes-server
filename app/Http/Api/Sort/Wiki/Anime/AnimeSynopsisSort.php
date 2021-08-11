<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Wiki\Anime;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Collection;

/**
 * Class AnimeSynopsisSort.
 */
class AnimeSynopsisSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'synopsis');
    }
}