<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class FakeCriteria.
 */
class FakeCriteria extends Criteria
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
        $expression = new Expression($filterValues);

        return new static(
            new Predicate($filterParam, null, $expression),
            BinaryLogicalOperator::getRandomInstance(),
            ScopeParser::parse(Str::random())
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
        return $builder;
    }
}
