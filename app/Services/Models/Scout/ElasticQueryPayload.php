<?php

declare(strict_types=1);

namespace App\Services\Models\Scout;

use App\Http\Api\Criteria\Search\Criteria;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;

/**
 * Class ElasticQueryPayload.
 */
abstract class ElasticQueryPayload
{
    /**
     * Filter set specified by the client.
     *
     * @var Criteria
     */
    protected Criteria $criteria;

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
    final public function __construct(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Create a new query payload instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Build Elasticsearch query.
     *
     * @return SearchRequestBuilder|BoolQueryBuilder
     */
    abstract public function buildQuery(): SearchRequestBuilder|BoolQueryBuilder;
}
