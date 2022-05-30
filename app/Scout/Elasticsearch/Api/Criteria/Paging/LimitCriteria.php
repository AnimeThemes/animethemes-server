<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Paging;

use App\Http\Api\Criteria\Paging\LimitCriteria as BaseCriteria;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use ElasticScoutDriverPlus\Paginator;
use Illuminate\Support\Collection;

/**
 * Class LimitCriteria.
 */
class LimitCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  BaseCriteria  $criteria
     */
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
    }

    /**
     * Paginate the search query.
     *
     * @param  SearchRequestBuilder  $builder
     * @return Collection|Paginator
     */
    public function paginate(SearchRequestBuilder $builder): Collection|Paginator
    {
        return $builder
            ->size($this->criteria->getResultSize())
            ->execute()
            ->models();
    }
}
