<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Http\Api\Criteria\Filter\Criteria;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class Filter.
 */
abstract class Filter
{
    /**
     * Criteria that may be applied to the builder.
     *
     * @var Collection<Criteria>
     */
    protected Collection $criteria;

    /**
     * Filter key value.
     *
     * @var string
     */
    protected string $key;

    /**
     * Filter scope.
     *
     * @var string
     */
    protected string $scope;

    /**
     * Create a new filter instance.
     *
     * @param Collection<Criteria> $criteria
     * @param string $key
     * @param string $scope
     */
    public function __construct(Collection $criteria, string $key, string $scope = '')
    {
        $this->criteria = $criteria;
        $this->key = $key;
        $this->scope = $scope;
    }

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
        return $this->getKey();
    }

    /**
     * Modify query builder with filter criteria.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function applyFilter(Builder $builder): Builder
    {
        foreach ($this->getCriteria() as $criterion) {
            if ($this->shouldApplyFilter($criterion)) {
                $builder = $criterion->applyFilter($builder, $this->getColumn(), $this->getFilterValues($criterion));
            }
        }

        return $builder;
    }

    /**
     * Modify search request builder with filter criteria.
     *
     * @param BoolQueryBuilder $builder
     * @return BoolQueryBuilder
     */
    public function applyElasticsearchFilter(BoolQueryBuilder $builder): BoolQueryBuilder
    {
        foreach ($this->getCriteria() as $criterion) {
            if ($this->shouldApplyFilter($criterion)) {
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
     * @param Criteria $criteria
     * @return bool
     */
    public function shouldApplyFilter(Criteria $criteria): bool
    {
        // Don't apply filter if scope does not match
        if (! $this->isMatchingScope($criteria)) {
            return false;
        }

        $filterValues = $this->getFilterValues($criteria);

        // Apply filter if we have a subset of valid values specified
        return ! empty($filterValues) && ! $this->isAllFilterValues($filterValues);
    }

    /**
     * Determine if the scope of this criterion matches the intended scope.
     *
     * @param Criteria $criteria
     * @return bool
     */
    protected function isMatchingScope(Criteria $criteria): bool
    {
        return empty($criteria->getScope()) || strcasecmp($criteria->getScope(), $this->getScope()) === 0;
    }

    /**
     * Get the filter criteria that match the filter key.
     *
     * @return Collection<Criteria>
     */
    public function getCriteria(): Collection
    {
        return $this->criteria->filter(function (Criteria $criteria) {
            return $criteria->getField() === $this->getKey();
        });
    }

    /**
     * Get sanitized filter values.
     *
     * @param Criteria $criteria
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
     * @param array $filterValues
     * @return array
     */
    protected function getUniqueFilterValues(array $filterValues): array
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     *
     * @param array $filterValues
     * @return array
     */
    abstract protected function convertFilterValues(array $filterValues): array;

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param array $filterValues
     * @return array
     */
    abstract protected function getValidFilterValues(array $filterValues): array;

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param array $filterValues
     * @return bool
     */
    abstract protected function isAllFilterValues(array $filterValues): bool;

    /**
     * Set intended scope of query.
     *
     * @param string $scope
     * @return $this
     */
    public function scope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get intended scope of query.
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }
}
