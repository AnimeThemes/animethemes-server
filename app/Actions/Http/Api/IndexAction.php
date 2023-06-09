<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Concerns\Actions\Http\Api\ConstrainsEagerLoads;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use App\Scout\Elasticsearch\Elasticsearch;
use App\Scout\Search;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use RuntimeException;

/**
 * Class IndexAction.
 */
class IndexAction
{
    use ConstrainsEagerLoads;

    /**
     * Show paginated listing of models.
     *
     * @param  Builder  $builder
     * @param  Query  $query
     * @param  Schema  $schema
     * @return Collection|Paginator
     */
    public function index(Builder $builder, Query $query, Schema $schema): Collection|Paginator
    {
        $builder->with($this->constrainEagerLoads($query, $schema));

        $this->select($builder, $query, $schema);

        $this->withAggregates($builder, $query, $schema);

        $scope = ScopeParser::parse($schema->type());
        $this->filter($builder, $query, $schema, $scope);

        // special case: only apply HasFilter to top-level models
        if (! empty($schema->allowedIncludes())) {
            $hasFilter = new HasFilter($schema->allowedIncludes());
            foreach ($query->getFilterCriteria() as $criteria) {
                if ($criteria->shouldFilter($hasFilter, $scope)) {
                    $criteria->filter($builder, $hasFilter, $query, $schema);
                }
            }
        }

        $this->sort($builder, $query, $schema, $scope);

        // paginate
        $paginationCriteria = $query->getPagingCriteria(PaginationStrategy::OFFSET);

        return $paginationCriteria !== null
            ? $paginationCriteria->paginate($builder)
            : $builder->get();
    }

    /**
     * Search models.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @param  PaginationStrategy  $paginationStrategy
     * @return Collection|Paginator
     */
    public function search(
        Query $query,
        Schema $schema,
        PaginationStrategy $paginationStrategy = PaginationStrategy::OFFSET
    ): Collection|Paginator {
        $search = $this->getSearch();

        if ($search !== null && $search->shouldSearch($query) && $schema instanceof EloquentSchema) {
            return $search->search($query, $schema, $paginationStrategy);
        }

        // Let developer know why search can't be performed
        $driver = Config::get('scout.driver');
        $term = $query->getSearchCriteria()?->getTerm();
        throw new RuntimeException("Can't search for term '$term' with driver '$driver' and type '{$schema->type()}'");
    }

    /**
     * Get the search instance.
     *
     * @return Search|null
     */
    protected function getSearch(): ?Search
    {
        return match (Config::get('scout.driver')) {
            'elastic' => App::make(Elasticsearch::class),
            default => null,
        };
    }
}
