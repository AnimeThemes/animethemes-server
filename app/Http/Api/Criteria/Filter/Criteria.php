<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class Criteria
{
    final public const PARAM_SEPARATOR = '-';

    final public const VALUE_SEPARATOR = ',';

    public function __construct(
        protected readonly Predicate $predicate,
        protected readonly BinaryLogicalOperator $operator,
        protected readonly Scope $scope
    ) {}

    /**
     * Get the scope of the criteria.
     */
    public function getScope(): Scope
    {
        return $this->scope;
    }

    /**
     * Get the field that the predicate is applying an expression on.
     */
    public function getField(): string
    {
        return $this->predicate->getColumn();
    }

    /**
     * Get the comparison operator.
     */
    public function getComparisonOperator(): ?ComparisonOperator
    {
        return $this->predicate->getOperator();
    }

    /**
     * Get the logical operator.
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
     * Determine if this filter should be applied.
     */
    public function shouldFilter(Filter $filter, Scope $scope): bool
    {
        // Don't apply filter if key does not match
        if ($this->getField() !== $filter->getKey()) {
            return false;
        }

        // Don't apply filter if scope does not match
        if (! $this->getScope()->isWithinScope($scope)) {
            return false;
        }

        $filterValues = $filter->getFilterValues($this->getFilterValues());

        // Apply filter if we have a subset of valid values specified
        return ! empty($filterValues) && ! $filter->isAllFilterValues($filterValues);
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    abstract public function filter(Builder $builder, Filter $filter, Query $query, Schema $schema): Builder;

    /**
     * Create a new criteria instance from query string.
     */
    abstract public static function make(Scope $scope, string $filterParam, mixed $filterValues): static;
}
