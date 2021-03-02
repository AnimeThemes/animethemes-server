<?php

namespace App\JsonApi\Filter;

use App\JsonApi\QueryParser;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Filter set specified by the client.
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
     * Create a new filter instance.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @param string $key
     */
    public function __construct(QueryParser $parser, string $key)
    {
        $this->parser = $parser;
        $this->key = $key;
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
        if ($this->shouldApplyFilter()) {
            return $builder->whereIn($this->getKey(), $this->getFilterValues());
        }

        return $builder;
    }

    /**
     * Determine if this filter should be applied.
     *
     * @return bool
     */
    public function shouldApplyFilter()
    {
        // Don't apply filter if not specified by the client
        if (! $this->hasFilter()) {
            return false;
        }

        $filterValues = $this->getFilterValues();

        // Don't apply filter if there is not a subset of valid values specified
        if (empty($filterValues) || $this->isAllFilterValues($filterValues)) {
            return false;
        }

        return true;
    }

    /**
     * Determines if the filter has been set by the client.
     *
     * @return bool
     */
    protected function hasFilter()
    {
        return $this->parser->hasFilter($this->getKey());
    }

    /**
     * Get sanitized filter values.
     *
     * @return array
     */
    public function getFilterValues()
    {
        return $this->getUniqueFilterValues(
            $this->convertFilterValues(
                $this->getValidFilterValues(
                    $this->parser->getFilter($this->getKey())
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
    protected function getUniqueFilterValues($filterValues)
    {
        return array_values(array_unique($filterValues));
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues($filterValues)
    {
        return $filterValues;
    }

    /**
     * Get only filter values that are valid. By default, all values are valid.
     *
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues($filterValues)
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
    protected function isAllFilterValues($filterValues)
    {
        return false;
    }
}
