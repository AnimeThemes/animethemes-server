<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class LimitCriteria.
 */
class LimitCriteria extends Criteria
{
    public const PARAM = 'limit';

    /**
     * Get the intended pagination strategy.
     *
     * @return PaginationStrategy
     */
    public function getStrategy(): PaginationStrategy
    {
        return PaginationStrategy::LIMIT();
    }

    /**
     * Paginate the query.
     *
     * @param  Builder  $builder
     * @return Collection|Paginator
     */
    public function paginate(Builder $builder): Collection|Paginator
    {
        return $builder->take($this->getResultSize())->get();
    }
}
