<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Parser;

use App\Http\Api\Criteria\Paging\Criteria as BaseCriteria;
use App\Http\Api\Criteria\Paging\LimitCriteria as BaseLimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria as BaseOffsetCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Paging\Criteria;
use App\Scout\Elasticsearch\Api\Criteria\Paging\LimitCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Paging\OffsetCriteria;

/**
 * Class PagingParser.
 */
class PagingParser
{
    /**
     * Parse Elasticsearch paging criteria from core paging criteria.
     *
     * @param  BaseCriteria  $criteria
     * @return Criteria|null
     */
    public static function parse(BaseCriteria $criteria): ?Criteria
    {
        if ($criteria instanceof BaseLimitCriteria) {
            return new LimitCriteria($criteria);
        }

        if ($criteria instanceof BaseOffsetCriteria) {
            return new OffsetCriteria($criteria);
        }

        return null;
    }
}
