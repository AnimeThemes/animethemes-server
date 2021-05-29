<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\TrashedStatus;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TrashedCondition extends Condition
{
    /**
     * Create a new condition instance.
     *
     * @param \App\JsonApi\Condition\Predicate $predicate
     * @param \App\Enums\Filter\BinaryLogicalOperator $operator
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
     * @param string $filterValues
     * @return \App\JsonApi\Condition\Condition
     */
    public static function make(string $filterParam, string $filterValues)
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
                continue;
            }
        }

        $expression = new Expression(Str::of($filterValues)->explode(','));

        $predicate = new Predicate($field, null, $expression);

        return new static($predicate, $logicalOperator, $scope);
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \App\JsonApi\Filter\Filter $filter
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function apply(Builder $builder, Filter $filter)
    {
        foreach ($filter->getFilterValues($this) as $filterValue) {
            switch (Str::lower($filterValue)) {
            case TrashedStatus::WITH:
                $builder = $builder->withTrashed();
                break;
            case TrashedStatus::WITHOUT:
                $builder = $builder->withoutTrashed();
                break;
            case TrashedStatus::ONLY:
                $builder = $builder->onlyTrashed();
                break;
            }
        }

        return $builder;
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param \ElasticScoutDriverPlus\Builders\BoolQueryBuilder $builder
     * @param \App\JsonApi\Filter\Filter $filter
     * @return \ElasticScoutDriverPlus\Builders\BoolQueryBuilder $builder
     */
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter)
    {
        return $builder;
    }
}
