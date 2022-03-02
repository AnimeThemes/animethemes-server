<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\WhereCriteria as BaseCriteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\QueryBuilderInterface;
use ElasticScoutDriverPlus\Support\Query as ElasticQuery;
use Illuminate\Support\Str;

/**
 * Class WhereCriteria.
 */
class WhereCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
    }

    /**
     * Apply criteria to builder.
     *
     * @param  BoolQueryBuilder  $builder
     * @param  Filter  $filter
     * @param  Query  $query
     * @return BoolQueryBuilder
     */
    public function filter(BoolQueryBuilder $builder, Filter $filter, Query $query): BoolQueryBuilder
    {
        $clause = $this->getElasticsearchClause($filter);

        if (BinaryLogicalOperator::OR()->is($this->criteria->getLogicalOperator())) {
            if (ComparisonOperator::NE()->is($this->criteria->getComparisonOperator())) {
                return $builder->should((new BoolQueryBuilder())->mustNot($clause));
            }

            return $builder->should($clause);
        }

        if (ComparisonOperator::NE()->is($this->criteria->getComparisonOperator())) {
            return $builder->mustNot($clause);
        }

        return $builder->must($clause);
    }

    /**
     * Build clause for Elasticsearch filter based on comparison operator.
     *
     * @param  Filter  $filter
     * @return QueryBuilderInterface
     */
    protected function getElasticsearchClause(Filter $filter): QueryBuilderInterface
    {
        $filterValue = $this->coerceFilterValue($filter);

        return match ($this->criteria->getComparisonOperator()?->value) {
            ComparisonOperator::LT => ElasticQuery::range()->field($filter->getColumn())->lt($filterValue),
            ComparisonOperator::GT => ElasticQuery::range()->field($filter->getColumn())->gt($filterValue),
            ComparisonOperator::LTE => ElasticQuery::range()->field($filter->getColumn())->lte($filterValue),
            ComparisonOperator::GTE => ElasticQuery::range()->field($filter->getColumn())->gte($filterValue),
            ComparisonOperator::LIKE => ElasticQuery::wildcard()
                ->field($filter->getColumn())
                ->value(Str::replace(['%', '_'], ['*', '?'], $filterValue)),
            ComparisonOperator::NOTLIKE => ElasticQuery::bool()->mustNot(
                ElasticQuery::wildcard()->field($filter->getColumn())->value(Str::replace(['%', '_'], ['*', '?'], $filterValue))
            ),
            default => ElasticQuery::term()->field($filter->getColumn())->value($filterValue),
        };
    }

    /**
     * Coerce filter value for elasticsearch range and term queries.
     *
     * @param  Filter  $filter
     * @return string
     */
    protected function coerceFilterValue(Filter $filter): string
    {
        $filterValue = collect($filter->getFilterValues($this->criteria->getFilterValues()))->first();

        // Elasticsearch wants 'true' or 'false' for boolean fields
        if (is_bool($filterValue)) {
            return $filterValue ? 'true' : 'false';
        }

        // The Elasticsearch driver wants a string for wildcard & term queries
        return strval($filterValue);
    }
}