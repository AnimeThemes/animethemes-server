<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Base;

use App\Http\Api\Filter\DateFilter;
use Illuminate\Support\Collection;

/**
 * Class CreatedAtFilter.
 */
class CreatedAtFilter extends DateFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'created_at');
    }
}
