<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Filter;

use App\Http\Api\Criteria\Filter\Criteria as BaseCriteria;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query\Query;
use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;

abstract class Criteria
{
    public function __construct(protected readonly BaseCriteria $criteria) {}

    abstract public function filter(BoolQueryBuilder $builder, Filter $filter, Query $query): BoolQueryBuilder;
}
