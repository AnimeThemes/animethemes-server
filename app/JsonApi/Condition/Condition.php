<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Filter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Condition
{
    /**
     * The type for which the condition is scoped.
     * If not set, the condition is globally scoped for all types.
     *
     * @var string
     */
    protected $scope;

    /**
     * The predicate of the condition.
     *
     * @var Predicate
     */
    protected $predicate;

    /**
     * The logical operator of the condition.
     *
     * @var BinaryLogicalOperator
     */
    protected $operator;

    /**
     * Create a new condition instance.
     *
     * @param Predicate $predicate
     * @param BinaryLogicalOperator $operator
     * @param string $scope
     */
    public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        string $scope = ''
    ) {
        $this->predicate = $predicate;
        $this->operator = $operator;
        $this->scope = $scope;
    }

    /**
     * Get the scope of the condition.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Get the field that the predicate is applying an expression on.
     *
     * @return string
     */
    public function getField()
    {
        return $this->predicate->getColumn();
    }

    /**
     * Get the comparison operator.
     *
     * @return ComparisonOperator|null $operator
     */
    public function getComparisonOperator()
    {
        return $this->predicate->getOperator();
    }

    /**
     * Get the logical operator.
     *
     * @return BinaryLogicalOperator
     */
    public function getLogicalOperator()
    {
        return $this->operator;
    }

    /**
     * Get the filter values.
     *
     * @return array
     */
    public function getFilterValues()
    {
        $value = $this->predicate->getExpression()->getValue();

        if (is_scalar($value)) {
            return [$value];
        }

        if ($value instanceof Collection) {
            return $value->all();
        }

        return (array) $value;
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    abstract public function apply(Builder $builder, Filter $filter);

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param string $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, string $filterValues)
    {
        if (Str::contains($filterValues, ',')) {
            return WhereInCondition::make($filterParam, $filterValues);
        }

        return WhereCondition::make($filterParam, $filterValues);
    }
}
