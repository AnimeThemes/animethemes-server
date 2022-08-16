<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Query;

use App\Http\Api\Criteria\Search\Criteria;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;

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
     * The schema this payload is searching.
     *
     * @return Schema
     */
    abstract public function schema(): Schema;

    /**
     * Build Elasticsearch query.
     *
     * @return SearchParametersBuilder
     */
    abstract public function buildQuery(): SearchParametersBuilder;
}
