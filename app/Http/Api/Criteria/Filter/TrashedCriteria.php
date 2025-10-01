<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TrashedCriteria extends Criteria
{
    final public const string PARAM_VALUE = 'trashed';

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
        $expression = new Expression(Str::of($filterValues)->explode(Criteria::VALUE_SEPARATOR));

        return new static(
            new Predicate(TrashedCriteria::PARAM_VALUE, null, $expression),
            BinaryLogicalOperator::AND,
            $scope
        );
    }

    public function filter(Builder $builder, Filter $filter, Query $query, Schema $schema): Builder
    {
        $filterValues = $filter->getFilterValues($this->getFilterValues());

        foreach ($filterValues as $filterValue) {
            $builder = match (Str::lower($filterValue)) {
                TrashedStatus::WITH->value => $builder->withTrashed(),
                TrashedStatus::WITHOUT->value => $builder->withoutTrashed(),
                TrashedStatus::ONLY->value => $builder->onlyTrashed(),
                default => $builder,
            };
        }

        return $builder;
    }
}
