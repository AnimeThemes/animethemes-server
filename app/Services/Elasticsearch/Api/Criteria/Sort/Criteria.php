<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Sort;

use App\Http\Api\Criteria\Sort\Criteria as BaseCriteria;
use App\Http\Api\Sort\Sort;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

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
    public function __construct(protected BaseCriteria $criteria)
    {
    }

    /**
     * Apply criteria to builder.
     *
     * @param SearchRequestBuilder $builder
     * @param Sort $sort
     * @return SearchRequestBuilder
     */
    abstract public function sort(SearchRequestBuilder $builder, Sort $sort): SearchRequestBuilder;
}
