<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Scope\Scope;
use App\Http\Api\Scope\ScopeParser;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use ElasticScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class WhereCriteria.
 */
class WhereCriteria extends Criteria
{
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
        $comparisonOperator = ComparisonOperator::EQ();
        $logicalOperator = BinaryLogicalOperator::AND();

        $filterParts = Str::of($filterParam)->explode('.');
        while ($filterParts->isNotEmpty()) {
            $filterPart = $filterParts->pop();

            // Set logical operator
            if ($scope->isEmpty() && empty($field) && BinaryLogicalOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $logicalOperator = BinaryLogicalOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set comparison operator
            if ($scope->isEmpty() && empty($field) && ComparisonOperator::hasKey(Str::upper($filterPart))) {
                try {
                    $comparisonOperator = ComparisonOperator::fromKey(Str::upper($filterPart));
                } catch (InvalidEnumKeyException $e) {
                    Log::error($e->getMessage());
                }
                continue;
            }

            // Set field
            if ($scope->isEmpty() && empty($field)) {
                $field = Str::lower($filterPart);
                continue;
            }

            // Set scope
            if (! empty($field)) {
                $scope->prepend(Str::lower($filterPart));
            }
        }

        $expression = new Expression($filterValues);

        return new static(
            new Predicate($field, $comparisonOperator, $expression),
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
        return $builder->where(
            $builder->qualifyColumn($column),
            $this->getComparisonOperator()?->value,
            collect($filterValues)->first(),
            $this->getLogicalOperator()->value
        );
    }

    /**
     * Apply criteria to builder through filter.
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
        $clause = $this->getElasticsearchClause($column, $filterValues);

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
     * @param  string  $column
     * @param  array  $filterValues
     * @return QueryBuilderInterface
     */
    protected function getElasticsearchClause(string $column, array $filterValues): QueryBuilderInterface
    {
        $filterValue = $this->coerceFilterValue($filterValues);

        return match ($this->getComparisonOperator()?->value) {
            ComparisonOperator::LT => Query::range()->field($column)->lt($filterValue),
            ComparisonOperator::GT => Query::range()->field($column)->gt($filterValue),
            ComparisonOperator::LTE => Query::range()->field($column)->lte($filterValue),
            ComparisonOperator::GTE => Query::range()->field($column)->gte($filterValue),
            ComparisonOperator::LIKE => Query::wildcard()
                ->field($column)
                ->value(Str::replace(['%', '_'], ['*', '?'], $filterValue)),
            ComparisonOperator::NOTLIKE => Query::bool()->mustNot(
                Query::wildcard()->field($column)->value(Str::replace(['%', '_'], ['*', '?'], $filterValue))
            ),
            default => Query::term()->field($column)->value($filterValue),
        };
    }

    /**
     * Coerce filter value for elasticsearch range and term queries.
     *
     * @param  array  $filterValues
     * @return string
     */
    protected function coerceFilterValue(array $filterValues): string
    {
        $filterValue = collect($filterValues)->first();

        // Elasticsearch wants 'true' or 'false' for boolean fields
        if (is_bool($filterValue)) {
            return $filterValue ? 'true' : 'false';
        }

        // The Elasticsearch driver wants a string for wildcard & term queries
        return strval($filterValue);
    }
}
