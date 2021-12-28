<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Http\Api\Criteria\Filter\Criteria;
use App\Http\Api\Scope\Scope;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class Filter.
 */
abstract class Filter
{
    /**
     * Create a new filter instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     */
    public function __construct(protected string $key, protected ?string $column = null) {}

    /**
     * Get filter key value.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get filter column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->key;
    }

    /**
     * Modify query builder with filter criteria.
     *
     * @param  Collection  $criteria
     * @param  Builder  $builder
     * @param  Scope  $scope
     * @return Builder
     */
    public function applyFilter(Collection $criteria, Builder $builder, Scope $scope): Builder
    {
        foreach ($criteria as $criterion) {
            if ($this->shouldApplyFilter($criterion, $scope)) {
                $builder = $criterion->applyFilter(
                    $builder,
                    $this->getColumn(),
                    $this->getFilterValues($criterion),
                    $criteria
                );
            }
        }

        return $builder;
    }

    /**
     * Modify search request builder with filter criteria.
     *
     * @param  Collection  $criteria
     * @param  BoolQueryBuilder  $builder
     * @param  Scope  $scope
     * @return BoolQueryBuilder
     */
    public function applyElasticsearchFilter(
        Collection $criteria,
        BoolQueryBuilder $builder,
        Scope $scope
    ): BoolQueryBuilder {
        foreach ($criteria as $criterion) {
            if ($this->shouldApplyFilter($criterion, $scope)) {
                $builder = $criterion->applyElasticsearchFilter(
                    $builder,
                    $this->getColumn(),
                    $this->getFilterValues($criterion)
                );
            }
        }

        return $builder;
    }

    /**
     * Determine if this filter should be applied.
     *
     * @param  Criteria  $criteria
     * @param  Scope  $scope
     * @return bool
     */
    public function shouldApplyFilter(Criteria $criteria, Scope $scope): bool
    {
        // Don't apply filter if key does not match
        if ($criteria->getField() !== $this->getKey()) {
            return false;
        }

        // Don't apply filter if scope does not match
        if (! $criteria->getScope()->isWithinScope($scope)) {
            return false;
        }

        $filterValues = $this->getFilterValues($criteria);

        // Apply filter if we have a subset of valid values specified
        return ! empty($filterValues) && ! $this->isAllFilterValues($filterValues);
    }

    /**
     * Get sanitized filter values.
     *
     * @param  Criteria  $criteria
     * @return array
     */
    public function getFilterValues(Criteria $criteria): array
    {
        return $this->getUniqueFilterValues(
            $this->convertFilterValues(
                $this->getValidFilterValues(
                    $criteria->getFilterValues()
                )
            )
        );
    }

    /**
     * Get unique filter values.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getUniqueFilterValues(array $filterValues): array
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     *
     * @param  array  $filterValues
     * @return array
     */
    abstract protected function convertFilterValues(array $filterValues): array;

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param  array  $filterValues
     * @return array
     */
    abstract protected function getValidFilterValues(array $filterValues): array;

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     * @return bool
     */
    abstract protected function isAllFilterValues(array $filterValues): bool;
}
