<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use App\Enums\Http\Api\Sort\Direction;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FieldSort.
 */
class FieldCriteria extends Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  string  $field
     * @param  Direction  $direction
     */
    public function __construct(string $field, protected Direction $direction)
    {
        parent::__construct($field);
    }

    /**
     * Get the sort direction.
     *
     * @return Direction
     */
    public function getDirection(): Direction
    {
        return $this->direction;
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
        return $builder->orderBy($column, $this->direction->value);
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
        return $builder->sort($column, $this->direction->value);
    }
}
