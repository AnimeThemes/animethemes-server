<?php declare(strict_types=1);

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\TrashedStatus;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class TrashedCondition
 * @package App\JsonApi\Condition
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
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    public function apply(Builder $builder, Filter $filter): Builder
    {
        foreach ($filter->getFilterValues($this) as $filterValue) {
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
     * Apply condition to builder through filter.
     *
     * @param BoolQueryBuilder $builder
     * @param Filter $filter
     * @return BoolQueryBuilder $builder
     */
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter): BoolQueryBuilder
    {
        return $builder;
    }
}
