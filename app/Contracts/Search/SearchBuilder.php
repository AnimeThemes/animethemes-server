<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Search\Criteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface SearchBuilder
{
    /**
     * Build the query.
     */
    public static function search(Model $model, Criteria $criteria): SearchBuilder;

    /**
     * Paginate the results.
     */
    public function withPagination(int $perPage, int $page): static;

    /**
     * Get the eloquent builder for the search results.
     */
    public function toEloquentBuilder(): Builder;

    /**
     * The keys of the retrieved models.
     *
     * @return int[]
     */
    public function keys(): array;
}
