<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseCriteria;
use App\Http\Api\Sort\Sort;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class FieldCriteria.
 */
class FieldCriteria extends Criteria
{
    protected Direction $direction;

    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
        $this->direction = $criteria->getDirection();
    }

    /**
     * Apply criteria to builder.
     *
     * @param SearchRequestBuilder $builder
     * @param Sort $sort
     * @return SearchRequestBuilder
     */
    public function sort(SearchRequestBuilder $builder, Sort $sort): SearchRequestBuilder
    {
        return $builder->sort($sort->getColumn(), $this->direction->value);
    }
}
