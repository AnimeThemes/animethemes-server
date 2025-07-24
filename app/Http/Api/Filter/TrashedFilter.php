<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Filter\UnaryLogicalOperator;
use App\Http\Api\Criteria\Filter\TrashedCriteria;

class TrashedFilter extends EnumFilter
{
    public function __construct()
    {
        parent::__construct(TrashedCriteria::PARAM_VALUE, TrashedStatus::class);
    }

    /**
     * Format filter string with conditions.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function format(
        BinaryLogicalOperator|UnaryLogicalOperator|null $logicalOperator = null,
        ?ComparisonOperator $comparisonOperator = null
    ): string {
        return $this->getKey();
    }
}
