<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Filter;

use App\Http\Api\Criteria\Filter\Criteria as BaseCriteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\ReadQuery;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;

/**
 * Class Criteria.
 */
abstract class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(protected readonly BaseCriteria $criteria)
    {
    }

    /**
     * Apply criteria to builder.
     *
     * @param  BoolQueryBuilder  $builder
     * @param  Filter  $filter
     * @param  ReadQuery  $query
     * @return BoolQueryBuilder
     */
    abstract public function filter(BoolQueryBuilder $builder, Filter $filter, ReadQuery $query): BoolQueryBuilder;
}