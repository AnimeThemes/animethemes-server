<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Search\Criteria;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
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
     * @param  array<string, array{direction: string, isString: bool, relation: ?string}>  $sorts
     */
    public function withSort(array $sorts): static;

    /**
     * Run a callback through the Eloquent query.
     */
    public function passToEloquentBuilder(Closure $callback): SearchBuilder;

    /**
     * Execute the search and get the resulting models.
     */
    public function execute(): Paginator;

    /**
     * Get the keys of the retrieved models.
     *
     * @return int[]
     */
    public function keys(): array;
}
