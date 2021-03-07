<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class WhereCondition extends Condition
{
    /**
     * Create a new condition instance.
     *
     * @param Predicate $predicate
     * @param BinaryLogicalOperator $operator
     * @param string $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        string $scope = ''
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param string $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, string $filterValues)
    {
        $scope = '';
        $field = '';
        $comparisonOperator = ComparisonOperator::fromValue(ComparisonOperator::EQ);
        $logicalOperator = BinaryLogicalOperator::fromValue(BinaryLogicalOperator::AND);

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($scope) && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                continue;
            }

            // Set comparison operator
            if (empty($scope) && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                continue;
            }

            // Set field
            if (empty($scope) && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set type
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filterPart);
                continue;
            }
        }

        $expression = new Expression($filterValues);

        $predicate = new Predicate($field, $comparisonOperator, $expression);

        return new static($predicate, $logicalOperator, $scope);
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    public function apply(Builder $builder, Filter $filter)
    {
        return $builder->where(
            $this->getField(),
            optional($this->getComparisonOperator())->value,
            collect($filter->getFilterValues($this))->first(),
            $this->getLogicalOperator()->value
        );
    }
}
