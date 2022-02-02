<?php

declare(strict_types=1);

namespace App\Http\Api\Query;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Include\Criteria as IncludeCriteria;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\BaseResource;
use App\Services\Elasticsearch\Elasticsearch;
use App\Services\Scout\Search;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Class EloquentQuery.
 */
abstract class EloquentQuery extends Query
{
    /**
     * Prepare model for json resource.
     *
     * @param  Model  $model
     * @return BaseResource
     */
    public function show(Model $model): BaseResource
    {
        $schema = $this->schema();

        $constrainedEagerLoads = $this->constrainEagerLoads(
            $this->getIncludeCriteria($schema->type())
        );

        return $this->resource($model->load($constrainedEagerLoads));
    }

    /**
     * Prepare models for resource collection.
     *
     * @return BaseCollection
     */
    public function index(): BaseCollection
    {
        $schema = $this->schema();

        // initialize builder, returning early if not resolved
        $builder = $this->builder();
        if ($builder === null) {
            return $this->collection(Collection::make());
        }

        // eager load relations with constraints
        $constrainedEagerLoads = $this->constrainEagerLoads(
            $this->getIncludeCriteria($schema->type())
        );
        $builder = $builder->with($constrainedEagerLoads);

        // apply filters
        $scope = ScopeParser::parse($schema->type());
        foreach ($this->getFilterCriteria() as $criteria) {
            foreach ($schema->filters() as $filter) {
                if ($criteria->shouldFilter($filter, $scope)) {
                    $builder = $criteria->filter($builder, $filter, $this);
                }
            }
        }

        // special case: only apply has filter to top-level models
        if (! empty($schema->allowedIncludes())) {
            $hasFilter = new HasFilter($schema->allowedIncludes());
            foreach ($this->getFilterCriteria() as $criteria) {
                if ($criteria->shouldFilter($hasFilter, $scope)) {
                    $builder = $criteria->filter($builder, $hasFilter, $this);
                }
            }
        }

        // apply sorts
        foreach ($this->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                if ($sortCriterion->shouldSort($sort)) {
                    $builder = $sortCriterion->sort($builder, $sort);
                }
            }
        }

        // paginate
        $paginationCriteria = $this->getPagingCriteria(PaginationStrategy::OFFSET());
        $collection = $paginationCriteria !== null
            ? $paginationCriteria->paginate($builder)
            : $builder->get();

        // return paginated resource collection
        return $this->collection($collection);
    }

    /**
     * Constrain eager loads by binding callbacks that filter on the relations.
     *
     * @param  IncludeCriteria|null  $includeCriteria
     * @return array
     */
    public function constrainEagerLoads(?IncludeCriteria $includeCriteria): array
    {
        $constrainedEagerLoads = [];

        $allowedIncludePaths = collect($includeCriteria?->getPaths());

        $schema = $this->schema();
        foreach ($allowedIncludePaths as $allowedIncludePath) {
            $scope = ScopeParser::parse($allowedIncludePath);
            $relationSchema = $schema->relation($allowedIncludePath);

            $constrainedEagerLoads[$allowedIncludePath] = function (Relation $relation) use ($scope, $relationSchema) {
                foreach ($this->getFilterCriteria() as $criteria) {
                    foreach (collect($relationSchema?->filters()) as $filter) {
                        if ($criteria->shouldFilter($filter, $scope)) {
                            $criteria->filter($relation->getQuery(), $filter, $this);
                        }
                    }
                }
            };
        }

        return $constrainedEagerLoads;
    }

    /**
     * Prepare models for sear.
     *
     * @param  PaginationStrategy  $paginationStrategy
     * @return BaseCollection
     */
    public function search(PaginationStrategy $paginationStrategy): BaseCollection
    {
        $search = static::getSearch();

        if ($search !== null && $search->shouldSearch($this)) {
            return $search->search($this, $paginationStrategy);
        }

        return $this->collection(Collection::make());
    }

    /**
     * Get the search instance.
     *
     * @return Search|null
     */
    protected static function getSearch(): ?Search
    {
        return match (Config::get('scout.driver')) {
            'elastic' => App::make(Elasticsearch::class),
            default => null,
        };
    }

    /**
     * Get the query builder of the resource.
     *
     * @return Builder|null
     */
    abstract public function builder(): ?Builder;

    /**
     * Get the json resource.
     *
     * @param  mixed  $resource
     * @return BaseResource
     */
    abstract public function resource(mixed $resource): BaseResource;

    /**
     * Get the resource collection.
     *
     * @param  mixed  $resource
     * @return BaseCollection
     */
    abstract public function collection(mixed $resource): BaseCollection;
}
