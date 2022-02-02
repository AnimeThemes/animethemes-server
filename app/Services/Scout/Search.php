<?php

declare(strict_types=1);

namespace App\Services\Scout;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\EloquentQuery;
use App\Http\Resources\BaseCollection;

/**
 * Class Search.
 */
abstract class Search
{
    /**
     * Should the search be performed?
     *
     * @param  EloquentQuery  $query
     * @return bool
     */
    abstract public function shouldSearch(EloquentQuery $query): bool;

    /**
     * Perform the search.
     *
     * @param  EloquentQuery  $query
     * @param  PaginationStrategy  $paginationStrategy
     * @return BaseCollection
     */
    abstract public function search(
        EloquentQuery $query,
        PaginationStrategy $paginationStrategy
    ): BaseCollection;
}
