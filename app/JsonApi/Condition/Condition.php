<?php

declare(strict_types=1);

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Condition
 * @package App\JsonApi\Condition
 */
abstract class Condition
{
    /**
     * The type for which the condition is scoped.
     * If not set, the condition is globally scoped for all types.
     *
     * @var string
     */
    protected string $scope;

    /**
     * The predicate of the condition.
     *
     * @var Predicate
     */
    protected Predicate $predicate;

    /**
     * The logical operator of the condition.
     *
     * @var BinaryLogicalOperator
     */
    protected BinaryLogicalOperator $operator;

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
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * Get the field that the predicate is applying an expression on.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->predicate->getColumn();
    }

    /**
     * Get the comparison operator.
     *
     * @return ComparisonOperator|null $operator
     */
    public function getComparisonOperator(): ?ComparisonOperator
    {
        return $this->predicate->getOperator();
    }

    /**
     * Get the logical operator.
     *
     * @return BinaryLogicalOperator
     */
    public function getLogicalOperator(): BinaryLogicalOperator
    {
        return $this->operator;
    }

    /**
     * Get the filter values.
     *
     * @return array
     */
    public function getFilterValues(): array
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
    abstract public function apply(Builder $builder, Filter $filter): Builder;

    /**
     * Apply condition to builder through filter.
     *
     * @param BoolQueryBuilder $builder
     * @param Filter $filter
     * @return BoolQueryBuilder $builder
     */
    abstract public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter): BoolQueryBuilder;

    /**
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param mixed $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, mixed $filterValues): Condition
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
