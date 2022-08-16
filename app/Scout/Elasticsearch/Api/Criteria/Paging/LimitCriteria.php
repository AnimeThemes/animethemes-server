<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Paging;

use App\Http\Api\Criteria\Paging\LimitCriteria as BaseCriteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Paginator;
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
     * @param  SearchParametersBuilder  $builder
     * @return Collection|Paginator
     */
    public function paginate(SearchParametersBuilder $builder): Collection|Paginator
    {
        return $builder
            ->size($this->criteria->getResultSize())
            ->execute()
            ->models();
    }
}
