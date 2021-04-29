<?php

namespace App\Concerns\JsonApi;

use App\Enums\JsonApi\PaginationStrategy;
use App\JsonApi\QueryParser;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait PerformsResourceCollectionSearch
{
    use PerformsConstrainedEagerLoading;

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @param \App\Enums\JsonApi\PaginationStrategy $paginationStrategy
     * @return static
     */
    public static function performSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ) {
        $driver = Config::get('scout.driver');

        // Perform Elasticsearch search
        if ($parser->hasSearch() && $driver === 'elastic') {
            return static::performElasticSearch($parser, $paginationStrategy);
        }

        // If we don't have a driver or search term, return an empty collection
        return static::make([], $parser);
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
     * Execute Elasticsearch query with resolved payload builder.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @param \App\Enums\JsonApi\PaginationStrategy $paginationStrategy
     * @return static
     */
    protected static function performElasticSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ) {
        $elasticQueryPayload = static::elasticQueryPayload($parser);

        // initialize builder with payload for matches
        $builder = $elasticQueryPayload->buildQuery();

        // eager load relations with constraints
        $builder = $builder->load(static::performConstrainedEagerLoads($parser));

        // apply filters
        $filterBuilder = new BoolQueryBuilder();
        foreach (static::filters() as $filterClass) {
            $filter = new $filterClass($parser);
            $filterBuilder = $filter->scope(Str::singular(static::$wrap))->applyElasticsearchFilter($filterBuilder);
        }
        try {
            $builder->filter($filterBuilder);
        } catch (QueryBuilderException $e) {
            // There doesn't appear to be a way to check if any filters have been set in the filter builder
        }

        // limit page size
        if (PaginationStrategy::LIMIT()->is($paginationStrategy)) {
            $builder = $builder->size($parser->getLimit());
        }

        // paginate
        if (PaginationStrategy::OFFSET()->is($paginationStrategy)) {
            $maxResults = $maxResults ?? Config::get('json-api-paginate.max_results');
            $defaultSize = $defaultSize ?? Config::get('json-api-paginate.default_size');
            $numberParameter = Config::get('json-api-paginate.number_parameter');
            $sizeParameter = Config::get('json-api-paginate.size_parameter');
            $paginationParameter = Config::get('json-api-paginate.pagination_parameter');

            $size = (int) request()->input($paginationParameter.'.'.$sizeParameter, $defaultSize);

            $size = $size > $maxResults ? $maxResults : $size;

            $paginator = $builder->paginate($size, $paginationParameter.'.'.$numberParameter)
                ->setPageName($paginationParameter.'['.$numberParameter.']')
                ->appends(Arr::except(request()->input(), $paginationParameter.'.'.$numberParameter));

            $paginator->setCollection($paginator->models());

            return static::make($paginator, $parser);
        }

        return static::make($builder->execute()->models(), $parser);
    }
}
