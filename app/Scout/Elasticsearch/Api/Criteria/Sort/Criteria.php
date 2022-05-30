<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Sort;

use App\Http\Api\Criteria\Sort\Criteria as BaseCriteria;
use App\Http\Api\Sort\Sort;

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
     * @param  Sort  $sort
     * @return array
     */
    abstract public function sort(Sort $sort): array;
}
