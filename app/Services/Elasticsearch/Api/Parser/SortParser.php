<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Parser;

use App\Http\Api\Criteria\Sort\Criteria as BaseCriteria;
use App\Http\Api\Criteria\Sort\FieldCriteria as BaseFieldCriteria;
use App\Http\Api\Criteria\Sort\RelationCriteria as BaseRelationCriteria;
use App\Services\Elasticsearch\Api\Criteria\Sort\Criteria;
use App\Services\Elasticsearch\Api\Criteria\Sort\FieldCriteria;
use App\Services\Elasticsearch\Api\Criteria\Sort\RelationCriteria;

/**
 * Class SortParser.
 */
class SortParser
{
    /**
     * Parse Elasticsearch sort criteria from core sort criteria.
     *
     * @param BaseCriteria $criteria
     * @return Criteria|null
     */
    public static function parse(BaseCriteria $criteria): ?Criteria
    {
        if ($criteria instanceof BaseRelationCriteria) {
            return new RelationCriteria($criteria);
        }

        if ($criteria instanceof BaseFieldCriteria) {
            return new FieldCriteria($criteria);
        }

        return null;
    }
}
