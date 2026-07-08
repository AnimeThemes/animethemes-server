<?php

declare(strict_types=1);

namespace App\Scout\Typesense;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria as PagingCriteria;
use App\Http\Api\Criteria\Sort\FieldCriteria;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Scope\ScopeParser;
use App\Scout\Criteria;
use App\Scout\Search;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class Typesense extends Search
{
    public function __construct(protected Model $model, Criteria $criteria)
    {
        parent::__construct($criteria);
    }

    /**
     * Perform the search.
     */
    public function searchViaJSONAPI(
        Query $query,
        EloquentSchema $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator {
        $model = $schema->model();

        /** @var ScoutBuilder $builder */
        /** @phpstan-ignore-next-line */
        $builder = $model::search($this->criteria->getTerm());
        $scope = ScopeParser::parse($schema->type());
        foreach ($query->getFilterCriteria() as $filter) {
            foreach ($schema->filters() as $schemaFilter) {
                if ($filter->shouldFilter($schemaFilter, $scope)) {
                    $builder->where(
                        $filter->getField(),
                        $filter->getComparisonOperator()->value,
                        $schemaFilter->getFilterValues($filter->getFilterValues()),
                    );
                }
            }
        }

        foreach ($query->getSortCriteria() as $sort) {
            foreach ($schema->sorts() as $schemaSort) {
                if ($sort->shouldSort($schemaSort, $scope) && $sort instanceof FieldCriteria) {
                    $builder->orderBy($sort->getField(), $sort->getDirection()->value);
                }
            }
        }

        $paginationCriteria = $query->getPagingCriteria($paginationStrategy);

        return $paginationCriteria instanceof PagingCriteria
            ? $builder->paginate($paginationCriteria->getResultSize())
            : $builder->get();
    }

    /**
     * @param  Closure(EloquentBuilder): void  $callback
     * @param  array<string, array{direction: string, isString: bool, relation: ?string}>  $sorts
     */
    public function search(?Closure $callback = null, int $perPage = 15, int $page = 1, array $sorts = []): LengthAwarePaginator
    {
        /** @phpstan-ignore-next-line */
        return $this->model::search($this->criteria->getTerm())
            ->query($callback)
            ->paginate($perPage, page: $page);
    }

    /**
     * Should the search be performed?
     */
    public function shouldSearch(): bool
    {
        return true;
    }
}
