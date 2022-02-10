<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Query;

use App\Http\Api\Criteria\Search\Criteria;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class ElasticQueryPayload.
 */
abstract class ElasticQueryPayload
{
    /**
     * Create a new query payload instance.
     *
     * @param  Criteria  $criteria
     */
    final public function __construct(protected Criteria $criteria)
    {
    }

    /**
     * The model this payload is searching.
     *
     * @return string
     */
    abstract public static function model(): string;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    abstract public function buildQuery(): SearchRequestBuilder;
}
