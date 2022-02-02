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
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model;

    /**
     * Create a new query payload instance.
     *
     * @param  Criteria  $criteria
     */
    final public function __construct(protected Criteria $criteria)
    {
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder
     */
    abstract public function buildQuery(): SearchRequestBuilder;
}
