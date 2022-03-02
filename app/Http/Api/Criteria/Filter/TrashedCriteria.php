<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class TrashedCriteria.
 */
class TrashedCriteria extends Criteria
{
    public const PARAM_VALUE = 'trashed';

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
     * @param  Scope  $scope
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(Scope $scope, string $filterParam, mixed $filterValues): static
    {
        $expression = new Expression(Str::of($filterValues)->explode(Criteria::VALUE_SEPARATOR));

        return new static(
            new Predicate(TrashedCriteria::PARAM_VALUE, null, $expression),
            BinaryLogicalOperator::AND(),
            $scope
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
        $filterValues = $filter->getFilterValues($this->getFilterValues());

        foreach ($filterValues as $filterValue) {
            $builder = match (Str::lower($filterValue)) {
                TrashedStatus::WITH => $builder->withTrashed(),
                TrashedStatus::WITHOUT => $builder->withoutTrashed(),
                TrashedStatus::ONLY => $builder->onlyTrashed(),
                default => $builder,
            };
        }

        return $builder;
    }
}
