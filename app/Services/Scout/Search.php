<?php

declare(strict_types=1);

namespace App\Services\Scout;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Query\Base\EloquentReadQuery;
use App\Http\Resources\BaseCollection;

/**
 * Class Search.
 */
abstract class Search
{
    /**
     * Should the search be performed?
     *
     * @param  EloquentReadQuery  $query
     * @return bool
     */
    abstract public function shouldSearch(EloquentReadQuery $query): bool;

    /**
     * Perform the search.
     *
     * @param  EloquentReadQuery  $query
     * @param  PaginationStrategy  $paginationStrategy
     * @return BaseCollection
     */
    abstract public function search(
        EloquentReadQuery $query,
        PaginationStrategy $paginationStrategy
    ): BaseCollection;
}
