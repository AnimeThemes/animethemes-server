<?php

declare(strict_types=1);

namespace App\Http\Api\Condition;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use ElasticScoutDriverPlus\Builders\AbstractParameterizedQueryBuilder;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\RangeQueryBuilder;
use ElasticScoutDriverPlus\Builders\TermQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereCondition.
 */
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
     * @param mixed $filterValues
     * @return Condition
     */
    public static function make(string $filterParam, mixed $filterValues): Condition
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
                try {
                    $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::info($e->getMessage());
                }
                continue;
            }

            // Set comparison operator
            if (empty($scope) && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::info($e->getMessage());
                }
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
    public function apply(Builder $builder, Filter $filter): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->getField()),
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
    public function applyElasticsearchFilter(BoolQueryBuilder $builder, Filter $filter): BoolQueryBuilder
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
     * @return AbstractParameterizedQueryBuilder
     */
    protected function getElasticsearchClause(Filter $filter): AbstractParameterizedQueryBuilder
    {
        return match (optional($this->getComparisonOperator())->value) {
            ComparisonOperator::LT => (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->lt(collect($filter->getFilterValues($this))->first()),
            ComparisonOperator::GT => (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->gt(collect($filter->getFilterValues($this))->first()),
            ComparisonOperator::LTE => (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->lte(collect($filter->getFilterValues($this))->first()),
            ComparisonOperator::GTE => (new RangeQueryBuilder())
                ->field($filter->getKey())
                ->gte(collect($filter->getFilterValues($this))->first()),
            default => (new TermQueryBuilder())
                ->field($filter->getKey())
                ->value(collect($filter->getFilterValues($this))->first()),
        };
    }
}
