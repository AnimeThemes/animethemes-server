<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\Clause;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WhereCriteria extends Criteria
{
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        Scope $scope
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Create a new criteria instance from query string.
     */
    public static function make(Scope $scope, string $filterParam, mixed $filterValues): static
    {
        $field = '';
        $comparisonOperator = ComparisonOperator::EQ;
        $logicalOperator = BinaryLogicalOperator::AND;

        $filterParts = Str::of($filterParam)->explode(Criteria::PARAM_SEPARATOR);
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($field) && BinaryLogicalOperator::unstrictCoerce($filterPart) instanceof BinaryLogicalOperator) {
                $logicalOperator = BinaryLogicalOperator::unstrictCoerce($filterPart);
                continue;
            }

            // Set comparison operator
            if (empty($field) && ComparisonOperator::unstrictCoerce($filterPart) instanceof ComparisonOperator) {
                $comparisonOperator = ComparisonOperator::unstrictCoerce($filterPart);
                continue;
            }

            // Set field
            if (empty($field)) {
                $field = Str::lower($filterPart);
            }
        }

        $expression = new Expression($filterValues);

        return new static(
            new Predicate($field, $comparisonOperator, $expression),
            $logicalOperator,
            $scope
        );
    }

    public function filter(Builder $builder, Filter $filter, Query $query, Schema $schema): Builder
    {
        $column = $filter->shouldQualifyColumn()
            ? $builder->qualifyColumn($filter->getColumn())
            : $filter->getColumn();

        return match ($filter->clause()) {
            Clause::WHERE => $builder->where(
                $column,
                $this->getComparisonOperator()?->value,
                Arr::first($filter->getFilterValues($this->getFilterValues())),
                $this->getLogicalOperator()->value
            ),
            Clause::HAVING => $builder->having(
                $column,
                $this->getComparisonOperator()?->value,
                Arr::first($filter->getFilterValues($this->getFilterValues())),
                $this->getLogicalOperator()->value
            ),
        };
    }
}
