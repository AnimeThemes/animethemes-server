<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Wiki\Studio;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Collection;

/**
 * Class StudioNameSort.
 */
class StudioNameSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'name');
    }
}
