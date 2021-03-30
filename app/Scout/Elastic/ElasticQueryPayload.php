<?php

namespace App\Scout\Elastic;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Collection;

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
     * Determine if this search should be performed.
     *
     * @return bool
     */
    protected function shouldPerformSearch()
    {
        return $this->parser->hasSearch();
    }

    /**
     * Perform search if condition is met, otherwise return empty collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
    {
        if ($this->shouldPerformSearch()) {
            return $this->doPerformSearch();
        }

        return Collection::make();
    }

    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    abstract protected function doPerformSearch();
}
