<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Paginator as ElasticsearchPaginator;
use Illuminate\Contracts\Pagination\Paginator as EloquentPaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
abstract class Criteria
{
    public const MAX_RESULTS = 30;

    public const DEFAULT_SIZE = 15;

    /**
     * The requested result size.
     *
     * @var int
     */
    protected int $resultSize;

    /**
     * Create a new criteria instance.
     *
     * @param  int  $resultSize
     */
    public function __construct(int $resultSize)
    {
        $this->resultSize = $resultSize;
    }

    /**
     * Get the validated result size.
     * Acceptable range is [1-30]. Default is 15.
     *
     * @return int
     */
    public function getResultSize(): int
    {
        if ($this->resultSize <= 0 || $this->resultSize > self::MAX_RESULTS) {
            return self::DEFAULT_SIZE;
        }

        return $this->resultSize;
    }

    /**
     * Get the intended pagination strategy.
     *
     * @return PaginationStrategy
     */
    abstract public function getStrategy(): PaginationStrategy;

    /**
     * Paginate the query.
     *
     * @param  Builder  $builder
     * @return Collection|EloquentPaginator
     */
    abstract public function applyPagination(Builder $builder): Collection|EloquentPaginator;

    /**
     * Paginate the search query.
     *
     * @param  SearchRequestBuilder  $builder
     * @return Collection|ElasticsearchPaginator
     */
    abstract public function applyElasticsearchPagination(SearchRequestBuilder $builder): Collection|ElasticsearchPaginator;
}
