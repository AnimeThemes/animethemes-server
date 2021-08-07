<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
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
     * @param Predicate $predicate
     * @param BinaryLogicalOperator $operator
     * @param string $scope
     */
    final public function __construct(
        Predicate $predicate,
        BinaryLogicalOperator $operator,
        string $scope = ''
    ) {
        parent::__construct($predicate, $operator, $scope);
    }

    /**
     * Create a new criteria instance from query string.
     *
     * @param string $filterParam
     * @param mixed $filterValues
     * @return static
     */
    public static function make(string $filterParam, mixed $filterValues): static
    {
        $scope = '';
        $field = '';
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set field
            if (empty($scope) && empty($field) && $filterPart === TrashedCriteria::PARAM_VALUE) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filterPart);
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        $predicate = new Predicate($field, null, $expression);

        return new static($predicate, $logicalOperator, $scope);
    }

    /**
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param string $column
     * @param array $filterValues
     * @return Builder
     */
    public function apply(Builder $builder, string $column, array $filterValues): Builder
    {
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
     * @param BoolQueryBuilder $builder
     * @param string $column
     * @param array $filterValues
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
