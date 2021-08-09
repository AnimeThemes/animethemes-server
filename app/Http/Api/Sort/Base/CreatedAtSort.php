<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Base;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Collection;

/**
 * Class CreatedAtSort.
 */
class CreatedAtSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'created_at');
    }
}
