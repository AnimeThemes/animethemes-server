<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Parser;

use App\Http\Api\Criteria\Filter\Criteria as BaseCriteria;
use App\Http\Api\Criteria\Filter\WhereCriteria as BaseWhereCriteria;
use App\Http\Api\Criteria\Filter\WhereInCriteria as BaseWhereInCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\Criteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Filter\WhereInCriteria;

class FilterParser
{
    /**
     * Parse Elasticsearch filter criteria from core filter criteria.
     */
    public static function parse(BaseCriteria $criteria): ?Criteria
    {
        if ($criteria instanceof BaseWhereCriteria) {
            return new WhereCriteria($criteria);
        }

        if ($criteria instanceof BaseWhereInCriteria) {
            return new WhereInCriteria($criteria);
        }

        return null;
    }
}
