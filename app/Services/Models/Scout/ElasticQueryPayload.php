<?php

declare(strict_types=1);

namespace App\Services\Models\Scout;

use App\Http\Api\QueryParser;
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
     * @var QueryParser
     */
    protected QueryParser $parser;

    /**
     * The model this payload is searching.
     *
     * @var string
     */
    public static string $model;

    /**
     * Create a new query payload instance.
     *
     * @param QueryParser $parser
     */
    final public function __construct(QueryParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Create a new query payload instance.
     *
     * @param mixed ...$parameters
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
