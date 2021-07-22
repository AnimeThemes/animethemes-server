<?php

declare(strict_types=1);

namespace App\Http\Api\Condition;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\TrashedStatus;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class TrashedCondition.
 */
class TrashedCondition extends Condition
{
    /**
     * Create a new condition instance.
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
     * Create a new condition instance from query string.
     *
     * @param string $filterParam
     * @param mixed $filterValues
     * @return Condition
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function make(string $filterParam, mixed $filterValues): Condition
    {
        $scope = '';
        $field = '';
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set field
            if (empty($scope) && empty($field) && strcasecmp($filterPart, 'trashed') === 0) {
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
     * Apply condition to builder.
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
     * Apply condition to builder.
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
