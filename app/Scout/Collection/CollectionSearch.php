<?php

declare(strict_types=1);

namespace App\Scout\Collection;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use App\Scout\Criteria;
use App\Scout\Search;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CollectionSearch extends Search
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
        // TODO
        return $this->search();
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
