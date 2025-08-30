<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class Criteria
{
    final public const MAX_RESULTS = 100;

    final public const DEFAULT_SIZE = 15;

    public function __construct(protected readonly int $resultSize) {}

    /**
     * Get the validated result size.
     * Acceptable range is [1-30]. Default is 15.
     */
    public function getResultSize(): int
    {
        if ($this->resultSize <= 0 || $this->resultSize > self::MAX_RESULTS) {
            return self::DEFAULT_SIZE;
        }

        return $this->resultSize;
    }

    abstract public function getStrategy(): PaginationStrategy;

    /**
     * Paginate the query.
     *
     * @param  Builder  $builder
     * @return Collection|Paginator
     */
    abstract public function paginate(Builder $builder): Collection|Paginator;
}
