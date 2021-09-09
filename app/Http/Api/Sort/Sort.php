<?php

declare(strict_types=1);

namespace App\Http\Api\Sort;

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\Criteria;
use ElasticScoutDriverPlus\Builders\SearchRequestBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Sort.
 */
abstract class Sort
{
    /**
     * Criteria that may be applied to the builder.
     *
     * @var Collection<Criteria>
     */
    protected Collection $criteria;

    /**
     * Sort key value.
     *
     * @var string
     */
    protected string $key;

    /**
     * Create a new sort instance.
     *
     * @param Collection<Criteria> $criteria
     * @param string $key
     */
    public function __construct(Collection $criteria, string $key)
    {
        $this->criteria = $criteria;
        $this->key = $key;
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
        return $this->getKey();
    }

    /**
     * Format the sort based on direction.
     *
     * @param Direction $direction
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
     * @param Builder $builder
     * @return Builder
     */
    public function applySort(Builder $builder): Builder
    {
        foreach ($this->getCriteria() as $criterion) {
            if ($this->shouldApplySort()) {
                $builder = $criterion->applySort($builder, $this->getColumn());
            }
        }

        return $builder;
    }

    /**
     * Modify search request builder with sort criteria.
     *
     * @param SearchRequestBuilder $builder
     * @return SearchRequestBuilder
     */
    public function applyElasticsearchSort(SearchRequestBuilder $builder): SearchRequestBuilder
    {
        foreach ($this->getCriteria() as $criterion) {
            if ($this->shouldApplySort()) {
                $builder = $criterion->applyElasticsearchSort($builder, $this->getColumn());
            }
        }

        return $builder;
    }

    /**
     * Determine if this sort should be applied.
     *
     * @return bool
     */
    public function shouldApplySort(): bool
    {
        return collect($this->getCriteria())->count() === 1;
    }

    /**
     * Get the sort criteria that match the sort key.
     *
     * @return Criteria[]
     */
    public function getCriteria(): array
    {
        return $this->criteria->filter(function (Criteria $criteria) {
            return $criteria->getField() === $this->getKey();
        })->all();
    }
}
