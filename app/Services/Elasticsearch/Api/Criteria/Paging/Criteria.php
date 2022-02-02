<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria as BaseCriteria;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Paginator;
use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
abstract class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(protected BaseCriteria $criteria)
    {
    }

    /**
     * Get the intended pagination strategy.
     *
     * @return PaginationStrategy
     */
    public function getStrategy(): PaginationStrategy
    {
        return $this->criteria->getStrategy();
    }

    /**
     * Paginate the search query.
     *
     * @param  SearchRequestBuilder  $builder
     * @return Collection|Paginator
     */
    abstract public function paginate(SearchRequestBuilder $builder): Collection|Paginator;
}
