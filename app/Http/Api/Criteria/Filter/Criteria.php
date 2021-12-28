<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Scope\Scope;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
abstract class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Predicate  $predicate
     * @param  BinaryLogicalOperator  $operator
     * @param  Scope  $scope
     */
    public function __construct(
        protected Predicate $predicate,
        protected BinaryLogicalOperator $operator,
        protected Scope $scope
    ) {}

    /**
     * Get the scope of the criteria.
     *
     * @return Scope
     */
    public function getScope(): Scope
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
     * @return ComparisonOperator|null
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

        if ($value instanceof Collection) {
            return $value->all();
        }

        return Arr::wrap($value);
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  string  $column
     * @param  array  $filterValues
     * @param  Collection  $filterCriteria
     * @return Builder
     */
    abstract public function applyFilter(
        Builder $builder,
        string $column,
        array $filterValues,
        Collection $filterCriteria
    ): Builder;

    /**
     * Apply criteria to builder.
     *
     * @param  BoolQueryBuilder  $builder
     * @param  string  $column
     * @param  array  $filterValues
     * @return BoolQueryBuilder
     */
    abstract public function applyElasticsearchFilter(
        BoolQueryBuilder $builder,
        string $column,
        array $filterValues
    ): BoolQueryBuilder;

    /**
     * Create a new criteria instance from query string.
     *
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    abstract public static function make(string $filterParam, mixed $filterValues): static;
}
