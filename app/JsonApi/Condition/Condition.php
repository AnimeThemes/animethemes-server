<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
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
     * @var \App\JsonApi\Condition\Predicate
     */
    protected $predicate;

    /**
     * The logical operator of the condition.
     *
     * @var \App\Enums\Filter\BinaryLogicalOperator
     */
    protected $operator;

    /**
     * Create a new condition instance.
     *
     * @param \App\JsonApi\Condition\Predicate $predicate
     * @param \App\Enums\Filter\BinaryLogicalOperator $operator
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
     * @return \App\Enums\Filter\ComparisonOperator|null $operator
     */
    public function getComparisonOperator()
    {
        return $this->predicate->getOperator();
    }

    /**
     * Get the logical operator.
     *
     * @return \App\Enums\Filter\BinaryLogicalOperator
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \App\JsonApi\Filter\Filter $filter
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    abstract public function apply(Builder $builder, Filter $filter);

    /**
     * Apply condition to builder through filter.
     *
     * @param \ElasticScoutDriverPlus\Builders\BoolQueryBuilder $builder
     * @param \App\JsonApi\Filter\Filter $filter
     * @return \ElasticScoutDriverPlus\Builders\BoolQueryBuilder $builder
     */
    abstract public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter);

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param string $filterValues
     * @return \App\JsonApi\Condition\Condition
     */
    public static function make(string $filterParam, string $filterValues)
    {
        if (Str::of($filterParam)->explode('.')->contains('trashed')) {
            return TrashedCondition::make($filterParam, $filterValues);
        }

        if (Str::contains($filterValues, ',')) {
            return WhereInCondition::make($filterParam, $filterValues);
        }

        return WhereCondition::make($filterParam, $filterValues);
    }
}
