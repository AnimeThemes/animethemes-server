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
use ElasticScoutDriverPlus\Builders\WildcardQueryBuilder;
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     * @return Builder
     */
    public function apply(Builder $builder, Filter $filter): Builder
    {
        return $builder->where(
            $builder->qualifyColumn($this->getField()),
            $this->getComparisonOperator()?->value,
            collect($filter->getFilterValues($this))->first(),
            $this->getLogicalOperator()->value
        );
    }

    /**
     * Apply condition to builder through filter.
     *
     * @param BoolQueryBuilder $builder
     * @param Filter $filter
     * @return BoolQueryBuilder
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
        $field = $filter->getKey();
        $filterValue = $this->coerceFilterValue($filter);

        return match ($this->getComparisonOperator()?->value) {
            ComparisonOperator::LT => (new RangeQueryBuilder())
                ->field($field)
                ->lt($filterValue),
            ComparisonOperator::GT => (new RangeQueryBuilder())
                ->field($field)
                ->gt($filterValue),
            ComparisonOperator::LTE => (new RangeQueryBuilder())
                ->field($field)
                ->lte($filterValue),
            ComparisonOperator::GTE => (new RangeQueryBuilder())
                ->field($field)
                ->gte($filterValue),
            ComparisonOperator::LIKE => (new WildcardQueryBuilder())
                ->field($field)
                ->value(Str::replace(['%', '_'], ['*', '?'], $filterValue)),
            ComparisonOperator::NOTLIKE => (new BoolQueryBuilder())->mustNot((new WildcardQueryBuilder())
                ->field($field)
                ->value(Str::replace(['%', '_'], ['*', '?'], $filterValue))),
            default => (new TermQueryBuilder())
                ->field($field)
                ->value($filterValue),
        };
    }

    /**
     * Coerce filter value for elasticsearch range and term queries.
     *
     * @param Filter $filter
     * @return string
     */
    protected function coerceFilterValue(Filter $filter): string
    {
        $filterValue = collect($filter->getFilterValues($this))->first();

        // Elasticsearch wants 'true' or 'false' for boolean fields
        if (is_bool($filterValue)) {
            return $filterValue ? 'true' : 'false';
        }

        // The Elasticsearch driver wants a string for wildcard & term queries
        return strval($filterValue);
    }
}
