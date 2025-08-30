<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class FakeCriteria extends Criteria
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
        $expression = new Expression($filterValues);

        return new static(
            new Predicate($filterParam, null, $expression),
            Arr::random(BinaryLogicalOperator::cases()),
            $scope
        );
    }

    /**
     * @param  Builder  $builder
     * @return Builder
     */
    public function filter(Builder $builder, Filter $filter, Query $query, Schema $schema): Builder
    {
        return $builder;
    }
}
