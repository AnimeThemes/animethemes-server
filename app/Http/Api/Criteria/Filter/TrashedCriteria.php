<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
     * @param  string  $filterParam
     * @param  mixed  $filterValues
     * @return static
     */
    public static function make(string $filterParam, mixed $filterValues): static
    {
        $scope = collect();
        $field = '';
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set field
            if ($scope->isEmpty() && empty($field) && $filterPart === TrashedCriteria::PARAM_VALUE) {
                $field = $filterPart;
                continue;
            }

            // Set scope
            if (! empty($field)) {
                $scope->prepend(Str::lower($filterPart));
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        return new static(
            new Predicate($field, null, $expression),
            $logicalOperator,
            ScopeParser::parse($scope->join('.'))
        );
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
    public function applyFilter(
        Builder $builder,
        string $column,
        array $filterValues,
        Collection $filterCriteria
    ): Builder {
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

    /**
     * Apply criteria to builder.
     *
     * @param  BoolQueryBuilder  $builder
     * @param  string  $column
     * @param  array  $filterValues
     * @return BoolQueryBuilder
     */
    public function applyElasticsearchFilter(
        BoolQueryBuilder $builder,
        string $column,
        array $filterValues
    ): BoolQueryBuilder {
        return $builder;
    }
}
