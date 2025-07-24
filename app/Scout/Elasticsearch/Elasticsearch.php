<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch;

use App\Concerns\Actions\Http\Api\AggregatesFields;
use App\Concerns\Actions\Http\Api\ConstrainsEagerLoads;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Scope\ScopeParser;
use App\Scout\Elasticsearch\Api\Parser\FilterParser;
use App\Scout\Elasticsearch\Api\Parser\PagingParser;
use App\Scout\Elasticsearch\Api\Parser\SortParser;
use App\Scout\Elasticsearch\Api\Schema\Schema;
use App\Scout\Search;
use Elastic\Client\ClientBuilderInterface;
use Elastic\ScoutDriverPlus\Builders\BoolQueryBuilder;
use Elastic\ScoutDriverPlus\Exceptions\QueryBuilderValidationException;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Elasticsearch extends Search
{
    use AggregatesFields;
    use ConstrainsEagerLoads;

    /**
     * Is the ES instance reachable?
     */
    protected bool $alive;

    public function __construct(ClientBuilderInterface $builder)
    {
        try {
            $this->alive = $builder->default()->ping()->asBool();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            $this->alive = false;
        }
    }

    /**
     * Is the ES instance reachable?
     */
    public function isAlive(): bool
    {
        return $this->alive;
    }

    /**
     * Should the search be performed?
     */
    public function shouldSearch(Query $query): bool
    {
        return $query->hasSearchCriteria() && $this->isAlive();
    }

    /**
     * Perform the search.
     *
     * @return Collection|Paginator
     */
    public function search(
        Query $query,
        EloquentSchema $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator {
        $elasticSchema = $this->elasticSchema($schema);

        $elasticQuery = $elasticSchema->query();

        // initialize builder for matches
        $builder = $elasticQuery->build($query->getSearchCriteria());

        // load aggregate fields
        $builder->refineModels(function (Builder $searchModelBuilder) use ($query, $schema) {
            $this->withAggregates($searchModelBuilder, $query, $schema);
        });

        // eager load relations with constraints
        $builder = $builder->load($this->constrainEagerLoads($query, $schema));

        // apply filters
        $filterBuilder = new BoolQueryBuilder();
        $scope = ScopeParser::parse($schema->type());
        foreach ($query->getFilterCriteria() as $filterCriterion) {
            $elasticFilterCriteria = FilterParser::parse($filterCriterion);
            if ($elasticFilterCriteria !== null) {
                foreach ($elasticSchema->filters() as $filter) {
                    if ($filterCriterion->shouldFilter($filter, $scope)) {
                        $filterBuilder = $elasticFilterCriteria->filter($filterBuilder, $filter, $query);
                    }
                }
            }
        }
        try {
            $builder->postFilter($filterBuilder);
        } catch (QueryBuilderValidationException) {
            // There doesn't appear to be a way to check if any filters have been set in the filter builder
        }

        // apply sorts
        $sorts = [];
        foreach ($query->getSortCriteria() as $sortCriterion) {
            $elasticSortCriteria = SortParser::parse($sortCriterion);
            if ($elasticSortCriteria !== null) {
                foreach ($elasticSchema->sorts() as $sort) {
                    if ($sortCriterion->shouldSort($sort, $scope)) {
                        $sorts[] = $elasticSortCriteria->sort($sort);
                    }
                }
            }
        }
        if (! empty($sorts)) {
            $builder->sortRaw($sorts);
        }

        // paginate
        $paginationCriteria = $query->getPagingCriteria($paginationStrategy);
        $elasticPaginationCriteria = PagingParser::parse($paginationCriteria);

        return $elasticPaginationCriteria !== null
            ? $elasticPaginationCriteria->paginate($builder)
            : $builder->execute()->models();
    }

    /**
     * Resolve Elasticsearch schema from Eloquent schema.
     */
    private function elasticSchema(EloquentSchema $schema): Schema
    {
        $elasticSchemaClass = Str::of(get_class($schema))
            ->replace('Http', 'Scout\\Elasticsearch')
            ->__toString();

        return new $elasticSchemaClass();
    }
}
