<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\LogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Criteria\Filter\TrashedCriteria;

/**
 * Class TrashedFilter.
 */
class TrashedFilter extends EnumFilter
{
    /**
     * Create a new filter instance.
     */
    public function __construct()
    {
        parent::__construct(TrashedCriteria::PARAM_VALUE, TrashedStatus::class);
    }

    /**
     * Format filter string with conditions.
     *
     * @param  LogicalOperator|null  $logicalOperator
     * @param  ComparisonOperator|null  $comparisonOperator
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function format(
        ?LogicalOperator $logicalOperator = null,
        ?ComparisonOperator $comparisonOperator = null
    ): string {
        return $this->getKey();
    }
}
