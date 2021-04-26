<?php

namespace App\Concerns\JsonApi;

use App\Enums\JsonApi\PaginationStrategy;
use App\JsonApi\QueryParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait PerformsResourceCollectionSearch
{
    use PerformsConstrainedEagerLoading;

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    public static function performSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ) {
        $driver = Config::get('scout.driver');

        // Perform Elasticsearch search
        if ($driver === 'elastic') {
            return static::performElasticSearch($parser, $paginationStrategy);
        }

        // If we don't have a driver configured, return an empty collection
        return static::make([], $parser);
    }

    /**
     * Resolve Elasticsearch query builder from collection class name.
     * We are assuming a convention of "{Model}Collection" to "{Model}QueryPayload".
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return \App\Scout\Elastic\ElasticQueryPayload
     */
    protected static function elasticQueryPayload(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ) {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $elasticQueryPayload = "\\App\\Scout\\Elastic\\{$model}QueryPayload";

        $relations = static::performConstrainedEagerLoads($parser);

        return $elasticQueryPayload::make($parser, $relations, $paginationStrategy);
    }

    /**
     * Execute Elasticsearch query with resolved payload builder.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    protected static function performElasticSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ) {
        $elasticQueryPayload = static::elasticQueryPayload($parser, $paginationStrategy);

        $collection = $elasticQueryPayload->performSearch();

        return static::make($collection, $parser);
    }
}
