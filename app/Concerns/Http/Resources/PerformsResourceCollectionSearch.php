<?php

declare(strict_types=1);

namespace App\Concerns\Http\Resources;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Api\QueryParser;
use App\Scout\Elastic\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Trait PerformsResourceCollectionSearch.
 */
trait PerformsResourceCollectionSearch
{
    /**
     * Perform query to prepare models for resource collection.
     *
     * @param QueryParser $parser
     * @param PaginationStrategy $paginationStrategy
     * @return static
     */
    public static function performSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ): static {
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
     * @param QueryParser $parser
     * @return ElasticQueryPayload
     */
    protected static function elasticQueryPayload(QueryParser $parser): ElasticQueryPayload
    {
        $model = Str::replaceLast('Collection', '', class_basename(static::class));

        $elasticQueryPayload = "\\App\\Scout\\Elastic\\{$model}QueryPayload";

        return $elasticQueryPayload::make($parser);
    }

    /**
     * Execute Elasticsearch query with resolved payload builder.
     *
     * @param QueryParser $parser
     * @param PaginationStrategy $paginationStrategy
     * @return static
     */
    protected static function performElasticSearch(
        QueryParser $parser,
        PaginationStrategy $paginationStrategy
    ): static {
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
        } catch (QueryBuilderException) {
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
