<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\Criteria;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class Sort.
 */
class Sort
{
    /**
     * Sort key value.
     *
     * @var string
     */
    protected string $key;

    /**
     * Sort key value.
     *
     * @var string|null
     */
    protected ?string $column;

    /**
     * Create a new sort instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     */
    public function __construct(string $key, ?string $column = null)
    {
        $this->key = $key;
        $this->column = $column;
    }

    /**
     * Get sort key value.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get sort column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Format the sort based on direction.
     *
     * @param  Direction  $direction
     * @return string
     */
    public function format(Direction $direction): string
    {
        return match ($direction->value) {
            Direction::DESCENDING => Str::of('-')->append($this->getKey())->__toString(),
            default => $this->getKey(),
        };
    }

    /**
     * Modify query builder with sort criteria.
     *
     * @param Criteria $criterion
     * @param  Builder  $builder
     * @return Builder
     */
    public function applySort(Criteria $criterion, Builder $builder): Builder
    {
        if ($this->shouldApplySort($criterion)) {
            $builder = $criterion->applySort($builder, $this->getColumn());
        }

        return $builder;
    }

    /**
     * Modify search request builder with sort criteria.
     *
     * @param Criteria $criterion
     * @param  SearchRequestBuilder  $builder
     * @return SearchRequestBuilder
     */
    public function applyElasticsearchSort(Criteria $criterion, SearchRequestBuilder $builder): SearchRequestBuilder
    {
        if ($this->shouldApplySort($criterion)) {
            $builder = $criterion->applyElasticsearchSort($builder, $this->getColumn());
        }

        return $builder;
    }

    /**
     * Determine if this sort should be applied.
     *
     * @param  Criteria  $criteria
     * @return bool
     */
    public function shouldApplySort(Criteria $criteria): bool
    {
        // Apply sort if key matches
        return $criteria->getField() === $this->getKey();
    }
}
