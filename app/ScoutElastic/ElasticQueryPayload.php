<?php

namespace App\ScoutElastic;

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
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    abstract public function performSearch();
}
