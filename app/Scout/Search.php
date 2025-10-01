<?php

declare(strict_types=1);

namespace App\Scout;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\EloquentSchema;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

abstract class Search
{
    /**
     * Should the search be performed?
     */
    abstract public function shouldSearch(Query $query): bool;

    /**
     * Perform the search.
     */
    abstract public function search(
        Query $query,
        EloquentSchema $schema,
        PaginationStrategy $paginationStrategy
    ): Collection|Paginator;
}
