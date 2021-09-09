<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Base;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Support\Collection;

/**
 * Class TrashedFilter.
 */
class TrashedFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, TrashedCriteria::PARAM_VALUE, TrashedStatus::class);
    }
}
