<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Sort;

use App\Http\Api\Criteria\Sort\Criteria as BaseCriteria;
use App\Http\Api\Sort\Sort;

abstract class Criteria
{
    public function __construct(protected readonly BaseCriteria $criteria) {}

    /**
     * @return array
     */
    abstract public function sort(Sort $sort): array;
}
