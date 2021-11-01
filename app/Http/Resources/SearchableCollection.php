<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query;
use App\Http\Api\Scope\ScopeParser;
use App\Services\Http\Resources\DiscoverElasticQueryPayload;
use App\Services\Models\Scout\ElasticQueryPayload;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * Class SearchableCollection.
 */
abstract class SearchableCollection extends BaseCollection
{
    /**
     * Perform query to prepare models for resource collection.
     *
     * @param  Query  $query
     * @param  PaginationStrategy  $paginationStrategy
     * @return static
     */
    public static function performSearch(
        Query $query,
        PaginationStrategy $paginationStrategy
    ): static {
        $driver = Config::get('scout.driver');

        // Perform Elasticsearch search
        if ($query->hasSearchCriteria() && $driver === 'elastic') {
            return static::performElasticSearch($query, $paginationStrategy);
        }

        // If we don't have a driver or search term, return an empty collection
        return static::make([], $query);
    }

    /**
     * Resolve Elasticsearch query builder from collection collects property.
     *
     * @param  Query  $query
     * @return ElasticQueryPayload|null
     */
    protected static function elasticQueryPayload(Query $query): ?ElasticQueryPayload
    {
        $collection = static::make(new MissingValue(), Query::make());

        $collectsClass = $collection->collects;

        $elasticQueryPayload = DiscoverElasticQueryPayload::byModelClass($collectsClass);

        if (! empty($elasticQueryPayload)) {
            return new $elasticQueryPayload($query->getSearchCriteria());
        }

        return null;
    }

    /**
     * Execute Elasticsearch query with resolved payload builder.
     *
     * @param  Query  $query
     * @param  PaginationStrategy  $paginationStrategy
     * @return static
     */
    protected static function performElasticSearch(
        Query $query,
        PaginationStrategy $paginationStrategy
    ): static {
        $elasticQueryPayload = static::elasticQueryPayload($query);
        if ($elasticQueryPayload === null) {
            return static::make(Collection::make(), $query);
        }

        // initialize builder with payload for matches
        $builder = $elasticQueryPayload->buildQuery();

        // eager load relations with constraints
        $constrainedEagerLoads = static::performConstrainedEagerLoads(
            $query->getResourceIncludeCriteria(static::$wrap),
            $query->getFilterCriteria()
        );
        $builder = $builder->load($constrainedEagerLoads);

        // apply filters
        $filterBuilder = new BoolQueryBuilder();
        $scope = ScopeParser::parse(static::$wrap);
        foreach (static::schema()->filters() as $filter) {
            $filterBuilder = $filter->applyElasticsearchFilter($query->getFilterCriteria(), $filterBuilder, $scope);
        }
        try {
            $builder->postFilter($filterBuilder);
        } catch (QueryBuilderException) {
            // There doesn't appear to be a way to check if any filters have been set in the filter builder
        }

        // apply sorts
        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (static::schema()->sorts() as $sort) {
                $builder = $sort->applyElasticsearchSort($sortCriterion, $builder);
            }
        }

        // paginate
        $paginationCriteria = $query->getPagingCriteria($paginationStrategy);
        $collection = $paginationCriteria !== null
            ? $paginationCriteria->applyElasticsearchPagination($builder)
            : $builder->execute()->models();

        return static::make($collection, $query);
    }
}
