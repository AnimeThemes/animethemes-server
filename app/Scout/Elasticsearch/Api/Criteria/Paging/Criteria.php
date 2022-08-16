<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Paging;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Api\Criteria\Paging\Criteria as BaseCriteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Paginator;
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
    public function __construct(protected readonly BaseCriteria $criteria)
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
     * @param  SearchParametersBuilder  $builder
     * @return Collection|Paginator
     */
    abstract public function paginate(SearchParametersBuilder $builder): Collection|Paginator;
}
