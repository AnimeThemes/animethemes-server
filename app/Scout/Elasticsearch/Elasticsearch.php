<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Resources\BaseCollection;
use App\Scout\Elasticsearch\Api\Parser\FilterParser;
use App\Scout\Elasticsearch\Api\Parser\PagingParser;
use App\Scout\Elasticsearch\Api\Parser\SortParser;
use App\Scout\Elasticsearch\Api\Query\ElasticQueryPayload;
use App\Scout\Search;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use ElasticScoutDriverPlus\Exceptions\QueryBuilderException;
use Elasticsearch\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class Elasticsearch.
 */
class Elasticsearch extends Search
{
    /**
     * Is the ES instance reachable?
     *
     * @var bool
     */
    protected bool $alive;

    /**
     * Create a new search instance.
     *
     * @param  Client  $client
     */
    public function __construct(Client $client)
    {
        try {
            $this->alive = $client->ping();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            $this->alive = false;
        }
    }

    /**
     * Is the ES instance reachable?
     *
     * @return bool
     */
    public function isAlive(): bool
    {
        return $this->alive;
    }

    /**
     * Should the search be performed?
     *
     * @param  EloquentReadQuery  $query
     * @return bool
     */
    public function shouldSearch(EloquentReadQuery $query): bool
    {
        return $query->hasSearchCriteria() && $this->isAlive();
    }

    /**
     * Perform the search.
     *
     * @param  EloquentReadQuery  $query
     * @param  PaginationStrategy  $paginationStrategy
     * @return BaseCollection
     */
    public function search(
        EloquentReadQuery $query,
        PaginationStrategy $paginationStrategy
    ): BaseCollection {
        $elasticQueryPayload = $this->elasticQueryPayload($query);
        if ($elasticQueryPayload === null) {
            $model = $query->schema()->model();
            throw new RuntimeException("ElasticQueryPayload not configured for model '$model'");
        }

        $schema = $query->schema();

        // initialize builder with payload for matches
        $builder = $elasticQueryPayload->buildQuery();

        // eager load relations with constraints
        $constrainedEagerLoads = $query->constrainEagerLoads();
        $builder = $builder->load($constrainedEagerLoads);

        // apply filters
        $filterBuilder = new BoolQueryBuilder();
        $scope = ScopeParser::parse($schema->type());
        foreach ($query->getFilterCriteria() as $filterCriterion) {
            $elasticFilterCriteria = FilterParser::parse($filterCriterion);
            if ($elasticFilterCriteria !== null) {
                foreach ($schema->filters() as $filter) {
                    if ($filterCriterion->shouldFilter($filter, $scope)) {
                        $filterBuilder = $elasticFilterCriteria->filter($filterBuilder, $filter, $query);
                    }
                }
            }
        }
        try {
            $builder->postFilter($filterBuilder);
        } catch (QueryBuilderException) {
            // There doesn't appear to be a way to check if any filters have been set in the filter builder
        }

        // apply sorts
        foreach ($query->getSortCriteria() as $sortCriterion) {
            $elasticSortCriteria = SortParser::parse($sortCriterion);
            if ($elasticSortCriteria !== null) {
                foreach ($schema->sorts() as $sort) {
                    if ($sortCriterion->shouldSort($sort, $scope)) {
                        $elasticSortCriteria->sort($builder, $sort);
                    }
                }
            }
        }

        // paginate
        $paginationCriteria = $query->getPagingCriteria($paginationStrategy);
        $elasticPaginationCriteria = PagingParser::parse($paginationCriteria);

        $collection = $elasticPaginationCriteria !== null
            ? $elasticPaginationCriteria->paginate($builder)
            : $builder->execute()->models();

        return $query->collection($collection);
    }

    /**
     * Resolve Elasticsearch query builder from schema.
     *
     * @param  EloquentReadQuery  $query
     * @return ElasticQueryPayload|null
     */
    protected function elasticQueryPayload(EloquentReadQuery $query): ?ElasticQueryPayload
    {
        $schema = $query->schema();

        $elasticQueryPayload = DiscoverElasticQueryPayload::byModelClass($schema->model());

        if ($elasticQueryPayload !== null) {
            return new $elasticQueryPayload($query->getSearchCriteria());
        }

        return null;
    }
}
