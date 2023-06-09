<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Concerns\Actions\Http\Api\FiltersModels;
use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class HasCriteria.
 */
class HasCriteria extends Criteria
{
    use FiltersModels {
        filter as filterModels;
    }

    final public const PARAM_VALUE = 'has';

    /**
     * Create a new criteria instance.
     *
     * @param  Predicate  $predicate
     * @param  BinaryLogicalOperator  $operator
     * @param  Scope  $scope
     * @param  int  $count
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        Scope $scope,
        protected readonly int $count = 1,
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Create a new criteria instance from query string.
     *
     * @param  Scope  $scope
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(Scope $scope, string $filterParam, mixed $filterValues): static
    {
        $field = '';
        $comparisonOperator = ComparisonOperator::GTE;
        $count = 1;
        $logicalOperator = BinaryLogicalOperator::AND;

        $filterParts = Str::of($filterParam)->explode(Criteria::PARAM_SEPARATOR);

        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($field) && BinaryLogicalOperator::unstrictCoerce($filterPart) !== null) {
                $logicalOperator = BinaryLogicalOperator::unstrictCoerce($filterPart);
                continue;
            }

            // Set count
            if (
                empty($field)
                && filter_var($filterPart, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $count = filter_var($filterPart, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                continue;
            }

            // Set comparison operator
            if (empty($field) && ComparisonOperator::unstrictCoerce($filterPart) !== null) {
                $comparisonOperator = ComparisonOperator::unstrictCoerce($filterPart);
                continue;
            }

            // Set found has param
            if (empty($field) && $filterPart === HasCriteria::PARAM_VALUE) {
                $field = $filterPart;
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(Criteria::VALUE_SEPARATOR));

        return new static(
            new Predicate(HasCriteria::PARAM_VALUE, $comparisonOperator, $expression),
            $logicalOperator,
            $scope,
            $count
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @param  Query  $query
     * @param  Schema  $schema
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, Query $query, Schema $schema): Builder
    {
        $filterValues = $filter->getFilterValues($this->getFilterValues());

        foreach ($filterValues as $filterValue) {
            $scope = ScopeParser::parse($filterValue);
            $relationSchema = $schema->relation($filterValue);

            $builder = $builder->has(
                $filterValue,
                $this->getComparisonOperator()?->value,
                $this->count,
                $this->getLogicalOperator()->value,
                function (Builder $relationBuilder) use ($scope, $query, $relationSchema) {
                    if ($relationSchema !== null) {
                        $this->filterModels($relationBuilder, $query, $relationSchema, $scope);
                    }
                }
            );
        }

        return $builder;
    }
}
