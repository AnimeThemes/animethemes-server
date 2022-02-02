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
 * Class WhereCriteria.
 */
class WhereCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  Predicate  $predicate
     * @param  BinaryLogicalOperator  $operator
     * @param  Scope  $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        Scope $scope
    ) {
        parent::__construct($predicate, $operator, $scope);
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
        $scope = collect();
        $field = '';
        $comparisonOperator = ComparisonOperator::EQ();
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if ($scope->isEmpty() && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set comparison operator
            if ($scope->isEmpty() && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set field
            if ($scope->isEmpty() && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (! empty($field)) {
                $scope->prepend(Str::lower($filterPart));
            }
        }

        $expression = new Expression($filterValues);

        return new static(
            new Predicate($field, $comparisonOperator, $expression),
            $logicalOperator,
            ScopeParser::parse($scope->join('.'))
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
        return $builder->where(
            $builder->qualifyColumn($filter->getColumn()),
            $this->getComparisonOperator()?->value,
            collect($filter->getFilterValues($this->getFilterValues())),
            $this->getLogicalOperator()->value
        );
    }
}
