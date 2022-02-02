<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class HasCriteria.
 */
class HasCriteria extends Criteria
{
    public const PARAM_VALUE = 'has';

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
        protected int $count = 1,
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
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(string $filterParam, mixed $filterValues): static
    {
        $scope = '';
        $field = '';
        $comparisonOperator = ComparisonOperator::GTE();
        $count = 1;
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($scope) && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set count
            if (
                empty($scope)
                && empty($field)
                && filter_var($filterPart, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null
            ) {
                $count = filter_var($filterPart, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                continue;
            }

            // Set comparison operator
            if (empty($scope) && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set found has param
            if (empty($scope) && empty($field) && $filterPart === HasCriteria::PARAM_VALUE) {
                $field = $filterPart;
                continue;
            }

            // Set scope
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filterPart);
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        return new static(
            new Predicate(HasCriteria::PARAM_VALUE, $comparisonOperator, $expression),
            $logicalOperator,
            ScopeParser::parse($scope),
            $count
        );
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  Filter  $filter
     * @param  Query  $query
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, Query $query): Builder
    {
        $schema = $query->schema();
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
                        foreach ($query->getFilterCriteria() as $criteria) {
                            foreach ($relationSchema->filters() as $filter) {
                                if ($criteria->shouldFilter($filter, $scope)) {
                                    $criteria->filter($relationBuilder, $filter, $query);
                                }
                            }
                        }
                    }
                }
            );
        }

        return $builder;
    }
}
