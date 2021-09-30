<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Resources\PerformsConstrainedEagerLoading;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Filter\HasFilter;
use App\Http\Api\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Scope\ScopeParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;

/**
 * Class BaseCollection.
 */
abstract class BaseCollection extends ResourceCollection
{
    use PerformsConstrainedEagerLoading;

    /**
     * Sparse field set specified by the client.
     *
     * @var Query
     */
    protected Query $query;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  Query  $query
     * @return void
     */
    public function __construct(mixed $resource, Query $query)
    {
        parent::__construct($resource);

        $this->query = $query;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    abstract public static function schema(): Schema;

    /**
     * Get the model query builder.
     *
     * @return Builder|null
     */
    protected static function queryBuilder(): ?Builder
    {
        $collection = static::make(new MissingValue(), Query::make());
        $collectsClass = $collection->collects;

        if (! empty($collectsClass)) {
            $collectsInstance = new $collectsClass();
            if ($collectsInstance instanceof Model) {
                return $collectsInstance::query();
            }
        }

        return null;
    }

    /**
     * Perform query to prepare models for resource collection.
     *
     * @param  Query  $query
     * @return static
     */
    public static function performQuery(Query $query): static
    {
        // initialize builder, returning early if not resolved
        $builder = static::queryBuilder();
        if ($builder === null) {
            return static::make(Collection::make(), $query);
        }

        // eager load relations with constraints
        $builder = $builder->with(static::performConstrainedEagerLoads($query));

        $schema = static::schema();

        // apply filters
        $scope = ScopeParser::parse(static::$wrap);
        foreach ($schema->filters() as $filter) {
            $builder = $filter->applyFilter($query->getFilterCriteria(), $builder, $scope);
        }

        // special case: only apply has filter to top-level models
        if (! empty($schema->allowedIncludes())) {
            $hasFilter = new HasFilter($schema->allowedIncludes());
            $hasFilter->applyFilter($query->getFilterCriteria(), $builder, $scope);
        }

        // apply sorts
        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                $builder = $sort->applySort($sortCriterion, $builder);
            }
        }

        // paginate
        $paginationCriteria = $query->getPagingCriteria(PaginationStrategy::OFFSET());
        $collection = $paginationCriteria !== null
            ? $paginationCriteria->applyPagination($builder)
            : $builder->get();

        // return paginated resource collection
        return static::make($collection, $query);
    }
}
