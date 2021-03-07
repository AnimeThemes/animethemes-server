<?php

namespace App\Concerns\JsonApi;

use App\JsonApi\QueryParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait PerformsResourceCollectionSearch
{
    /**
     * Perform query to prepare models for resource collection.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    public static function performSearch(QueryParser $parser)
    {
        $driver = Config::get('scout.driver');

        // Perform Elasticsearch search
        if ($driver === 'elastic') {
            return static::performElasticSearch($parser);
        }

        // Default: perform scout search
        return static::performScoutSearch($parser);
    }

    /**
     * Resolve Elasticsearch query builder from collection class name.
     * We are assuming a convention of "{Model}Collection" to "{Model}QueryPayload".
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return \App\Scout\Elastic\ElasticQueryPayload
     */
    protected static function elasticQueryPayload(QueryParser $parser)
    {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $elasticQueryPayload = "\\App\\Scout\\Elastic\\{$model}QueryPayload";

        return $elasticQueryPayload::make($parser);
    }

    /**
     * Resolve scout query builder from collection class name.
     * We are assuming a convention of "{Model}Collection".
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return \Laravel\Scout\Builder
     */
    protected static function searchBuilder(QueryParser $parser)
    {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $modelClass = "\\App\\Models\\{$model}";

        return $modelClass::search($parser->getSearch());
    }

    /**
     * Execute Elasticsearch query with resolved payload builder.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    protected static function performElasticSearch(QueryParser $parser)
    {
        $elasticQueryPayload = static::elasticQueryPayload($parser);

        $collection = $elasticQueryPayload->performSearch();

        return static::make($collection, $parser);
    }

    /**
     * Execute scout query with resolved builder.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    protected static function performScoutSearch(QueryParser $parser)
    {
        $builder = static::searchBuilder($parser);

        $collection = $builder->take($parser->getLimit())
            ->get()
            ->load($parser->getResourceIncludePaths(static::allowedIncludePaths(), Str::singular(static::$wrap)));

        return static::make($collection, $parser);
    }
}
