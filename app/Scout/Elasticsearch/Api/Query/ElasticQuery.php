<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Http\Api\Criteria\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;

/**
 * Class ElasticQuery.
 */
abstract class ElasticQuery
{
    /**
     * Build Elasticsearch query.
     *
     * @param  Criteria  $criteria
     * @return SearchParametersBuilder
     */
    abstract public function build(Criteria $criteria): SearchParametersBuilder;
}
