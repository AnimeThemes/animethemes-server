<?php

namespace App\JsonApi\Condition;

use App\Enums\Filter\BinaryLogicalOperator;
use App\Enums\Filter\ComparisonOperator;
use App\JsonApi\Filter\Filter;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class WhereCondition extends Condition
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
     * @param string $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, string $filterValues)
    {
        $scope = '';
        $field = '';
        $comparisonOperator = ComparisonOperator::EQ();
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if (empty($scope) && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                continue;
            }

            // Set comparison operator
            if (empty($scope) && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                continue;
            }

            // Set field
            if (empty($scope) && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (empty($scope) && ! empty($field)) {
                $scope = Str::lower($filterPart);
                continue;
            }
        }

        $expression = new Expression($filterValues);

        $predicate = new Predicate($field, $comparisonOperator, $expression);

        return new static($predicate, $logicalOperator, $scope);
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param Builder $builder
     * @param Filter $filter
     * @return Builder $builder
     */
    public function apply(Builder $builder, Filter $filter)
    {
        return $builder->where(
            $filter->getScope().'.'.$this->getField(),
            optional($this->getComparisonOperator())->value,
            collect($filter->getFilterValues($this))->first(),
            $this->getLogicalOperator()->value
        );
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param BoolQueryBuilder $builder
     * @param Filter $filter
     * @return BoolQueryBuilder $builder
     */
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter)
    {
        $clause = $this->getElasticsearchClause($filter);

        if (BinaryLogicalOperator::OR()->is($this->getLogicalOperator())) {
            if (ComparisonOperator::NE()->is($this->getComparisonOperator())) {
                return $builder->should((new BoolQueryBuilder())->mustNot($clause));
            }

            return $builder->should($clause);
        }

        if (ComparisonOperator::NE()->is($this->getComparisonOperator())) {
            return $builder->mustNot($clause);
        }

        return $builder->must($clause);
    }

    /**
     * Build clause for Elasticsearch filter based on comparison operator.
     *
     * @param Filter $filter
     * @return \ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder
     */
    protected function getElasticsearchClause(Filter $filter)
    {
        if (ComparisonOperator::LT()->is($this->getComparisonOperator())) {
            return (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->lt(collect($filter->getFilterValues($this))->first());
        }

        if (ComparisonOperator::GT()->is($this->getComparisonOperator())) {
            return (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->gt(collect($filter->getFilterValues($this))->first());
        }

        if (ComparisonOperator::LTE()->is($this->getComparisonOperator())) {
            return (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->lte(collect($filter->getFilterValues($this))->first());
        }

        if (ComparisonOperator::GTE()->is($this->getComparisonOperator())) {
            return (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->gte(collect($filter->getFilterValues($this))->first());
        }

        return (new TermQueryBuilder())
            ->field($filter->getKey())
            ->value(collect($filter->getFilterValues($this))->first());
    }
}
