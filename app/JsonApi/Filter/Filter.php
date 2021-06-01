<?php

declare(strict_types=1);

namespace App\JsonApi\Filter;

use App\JsonApi\Condition\Condition;
use App\JsonApi\QueryParser;
use ElasticScoutDriverPlus\Builders\BoolQueryBuilder;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package App\JsonApi\Filter
 */
abstract class Filter
{
    /**
     * Filters specified by the client.
     *
     * @var QueryParser
     */
    protected QueryParser $parser;

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
     * @param QueryParser $parser
     * @param string $key
     * @param string $scope
     */
    public function __construct(QueryParser $parser, string $key, string $scope = '')
    {
        $this->parser = $parser;
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
     * Modify query builder with filter criteria.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function applyFilter(Builder $builder): Builder
    {
        foreach ($this->getConditions() as $condition) {
            if ($this->shouldApplyFilter($condition)) {
                $builder = $condition->apply($builder, $this);
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
        foreach ($this->getConditions() as $condition) {
            if ($this->shouldApplyFilter($condition)) {
                $builder = $condition->applyElasticsearchFilter($builder, $this);
            }
        }

        return $builder;
    }

    /**
     * Determine if this filter should be applied.
     *
     * @param Condition $condition
     * @return bool
     */
    public function shouldApplyFilter(Condition $condition): bool
    {
        // Don't apply filter if scope does not match
        if (! $this->isMatchingScope($condition)) {
            return false;
        }

        $filterValues = $this->getFilterValues($condition);

        // Don't apply filter if there is not a subset of valid values specified
        if (empty($filterValues) || $this->isAllFilterValues($filterValues)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the scope of this condition matches the intended scope.
     *
     * @param Condition $condition
     * @return bool
     */
    protected function isMatchingScope(Condition $condition): bool
    {
        return empty($condition->getScope()) || strcasecmp($condition->getScope(), $this->getScope()) === 0;
    }

    /**
     * Get the underlying query conditions.
     *
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->parser->getConditions($this->getKey());
    }

    /**
     * Get sanitized filter values.
     *
     * @param Condition $condition
     * @return array
     */
    public function getFilterValues(Condition $condition): array
    {
        return $this->getUniqueFilterValues(
            $this->convertFilterValues(
                $this->getValidFilterValues(
                    $condition->getFilterValues()
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
    protected function convertFilterValues(array $filterValues): array
    {
        return $filterValues;
    }

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return $filterValues;
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param array $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }

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
