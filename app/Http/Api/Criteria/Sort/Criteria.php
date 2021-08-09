<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Sort;

use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Sort.
 */
abstract class Criteria
{
    /**
     * The sort field.
     *
     * @var string
     */
    protected string $field;

    /**
     * Create a new criteria instance.
     *
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * Get the criteria field.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Apply criteria to builder.
     *
     * @param Builder $builder
     * @param string $column
     * @return Builder
     */
    abstract public function applySort(Builder $builder, string $column): Builder;

    /**
     * Apply criteria to builder.
     *
     * @param SearchRequestBuilder $builder
     * @param string $column
     * @return SearchRequestBuilder
     */
    abstract public function applyElasticsearchSort(
        SearchRequestBuilder $builder,
        string $column
    ): SearchRequestBuilder;
}
