<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Http\Api\Criteria\Filter\WhereInCriteria as BaseCriteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\ReadQuery;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Support\Query as ElasticQuery;

/**
 * Class WhereInCriteria.
 */
class WhereInCriteria extends Criteria
{
    protected readonly bool $not;

    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
        $this->not = $criteria->not();
    }

    /**
     * Apply criteria to builder.
     *
     * @param  BoolQueryBuilder  $builder
     * @param  Filter  $filter
     * @param  ReadQuery  $query
     * @return BoolQueryBuilder
     */
    public function filter(BoolQueryBuilder $builder, Filter $filter, ReadQuery $query): BoolQueryBuilder
    {
        $filterValues = $filter->getFilterValues($this->criteria->getFilterValues());

        $clause = ElasticQuery::terms()->field($filter->getColumn())->values($filterValues);

        if (BinaryLogicalOperator::OR()->is($this->criteria->getLogicalOperator())) {
            if ($this->not) {
                return $builder->should((new BoolQueryBuilder())->mustNot($clause));
            }

            return $builder->should($clause);
        }

        if ($this->not) {
            return $builder->mustNot($clause);
        }

        return $builder->must($clause);
    }
}
