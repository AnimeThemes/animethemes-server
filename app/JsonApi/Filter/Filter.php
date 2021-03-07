<?php

namespace App\JsonApi\Filter;

use App\JsonApi\Condition\Condition;
use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Filters specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Filter key value.
     *
     * @var string
     */
    protected $key;

    /**
     * Filter scope.
     *
     * @var string
     */
    protected $scope;

    /**
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @param string $key
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Modify query builder with filter criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyFilter(Builder $builder)
    {
        foreach ($this->getConditions() as $condition) {
            if ($this->shouldApplyFilter($condition)) {
                $builder = $condition->apply($builder, $this);
            }
        }

        return $builder;
    }

    /**
     * Determine if this filter should be applied.
     *
     * @param \App\JsonApi\Condition\Condition $condition
     * @return bool
     */
    public function shouldApplyFilter(Condition $condition)
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
     * @param \App\JsonApi\Condition\Condition $condition
     * @return bool
     */
    protected function isMatchingScope(Condition $condition)
    {
        return empty($condition->getScope()) || strcasecmp($condition->getScope(), $this->getScope()) === 0;
    }

    /**
     * Get the underlying query conditions.
     *
     * @return \App\JsonApi\Condition\Condition[]
     */
    public function getConditions()
    {
        return $this->parser->getConditions($this->getKey());
    }

    /**
     * Get sanitized filter values.
     *
     * @param \App\JsonApi\Condition\Condition $condition
     * @return array
     */
    public function getFilterValues(Condition $condition)
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
    protected function getUniqueFilterValues(array $filterValues)
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues)
    {
        return $filterValues;
    }

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues)
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
    protected function isAllFilterValues(array $filterValues)
    {
        return false;
    }

    /**
     * Set intended scope of query.
     *
     * @param string $scope
     * @return $this
     */
    public function scope(string $scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get intended scope of query.
     *
     * @return string
     */
    protected function getScope()
    {
        return $this->scope;
    }
}
