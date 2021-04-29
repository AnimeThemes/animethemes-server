<?php

namespace App\Scout\Elastic;

use App\JsonApi\QueryParser;

abstract class ElasticQueryPayload
{
    /**
     * Filter set specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Create a new query payload instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     */
    final public function __construct(QueryParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Create a new query payload instance.
     *
     * @param  mixed  ...$parameters
     * @return static
     */
    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }

    /**
     * Build Elasticsearch query.
     *
     * @return \ElasticScoutDriverPlus\Builders\SearchRequestBuilder
     */
    abstract public function buildQuery();
}
