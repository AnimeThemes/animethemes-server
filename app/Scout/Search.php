<?php

declare(strict_types=1);

namespace App\Scout;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class Search.
 */
abstract class Search
{
    /**
     * Should the search be performed?
     *
     * @param  Query  $query
     * @return bool
     */
    abstract public function shouldSearch(Query $query): bool;

    /**
     * Perform the search.
     *
     * @param  Query  $query
     * @param  EloquentSchema  $schema
     * @param  PaginationStrategy  $paginationStrategy
     * @return Collection|Paginator
     */
    abstract public function search(
        Query $query,
        EloquentSchema  $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator;
}
