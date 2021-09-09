<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Resources\PerformsConstrainedEagerLoading;
use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Filter\Criteria as FilterCriteria;
use App\Http\Api\Criteria\Sort\Criteria as SortCriteria;
use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\HasFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Query;
use App\Http\Api\Scope\ScopeParser;
use App\Http\Api\Sort\Base\CreatedAtSort;
use App\Http\Api\Sort\Base\DeletedAtSort;
use App\Http\Api\Sort\Base\UpdatedAtSort;
use App\Http\Api\Sort\RandomSort;
use App\Http\Api\Sort\Sort;
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
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    abstract public static function allowedIncludePaths(): array;

    /**
     * The sorts that can be applied by the client for this resource.
     *
     * @param  Collection<SortCriteria>  $sortCriteria
     * @return Sort[]
     */
    public static function sorts(Collection $sortCriteria): array
    {
        return [
            new CreatedAtSort($sortCriteria),
            new UpdatedAtSort($sortCriteria),
            new DeletedAtSort($sortCriteria),
            new RandomSort($sortCriteria),
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @param  Collection<FilterCriteria>  $filterCriteria
     * @return Filter[]
     */
    public static function filters(Collection $filterCriteria): array
    {
        return [
            new CreatedAtFilter($filterCriteria),
            new UpdatedAtFilter($filterCriteria),
            new DeletedAtFilter($filterCriteria),
            new TrashedFilter($filterCriteria),
        ];
    }

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

        // apply filters
        $scope = ScopeParser::parse(static::$wrap);
        foreach (static::filters($query->getFilterCriteria()) as $filter) {
            $builder = $filter->applyFilter($builder, $scope);
        }

        // special case: only apply has filter to top-level models
        if (! empty(static::allowedIncludePaths())) {
            $hasFilter = new HasFilter($query->getFilterCriteria(), static::allowedIncludePaths());
            $hasFilter->applyFilter($builder, $scope);
        }

        // apply sorts
        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (static::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
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
