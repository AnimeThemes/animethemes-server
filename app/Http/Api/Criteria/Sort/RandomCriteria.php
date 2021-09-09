<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class RandomSort.
 */
class RandomCriteria extends Criteria
{
    public const PARAM_VALUE = 'random';

    /**
     * Create a new criteria instance.
     */
    public function __construct()
    {
        parent::__construct(self::PARAM_VALUE);
    }

    /**
     * Apply criteria to builder.
     *
     * @param  Builder  $builder
     * @param  string  $column
     * @return Builder
     */
    public function applySort(Builder $builder, string $column): Builder
    {
        return $builder->inRandomOrder();
    }

    /**
     * Apply criteria to builder.
     *
     * @param  SearchRequestBuilder  $builder
     * @param  string  $column
     * @return SearchRequestBuilder
     */
    public function applyElasticsearchSort(SearchRequestBuilder $builder, string $column): SearchRequestBuilder
    {
        return $builder;
    }
}
