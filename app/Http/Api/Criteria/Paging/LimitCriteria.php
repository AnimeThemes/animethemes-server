<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Paginator as ElasticsearchPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
     * @param Builder $builder
     * @return Collection|Paginator
     */
    public function apply(Builder $builder): Collection | Paginator
    {
        return $builder->take($this->getResultSize())->get();
    }

    /**
     * Paginate the search query.
     *
     * @param SearchRequestBuilder $builder
     * @return Collection|ElasticsearchPaginator
     */
    public function applyElasticsearch(SearchRequestBuilder $builder): Collection | ElasticsearchPaginator
    {
        return $builder
            ->size($this->getResultSize())
            ->execute()
            ->models();
    }
}
