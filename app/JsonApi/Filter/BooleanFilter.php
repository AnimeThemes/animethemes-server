<?php

namespace App\JsonApi\Filter;

abstract class BooleanFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues($filterValues)
    {
        return array_map(
            function ($filterValue) {
                return filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid boolean options.
     * Accepted for true: "1", "true", "on" and "yes".
     * Accepted for false: "0", "false", "off" and "no.
     *
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues($filterValues)
    {
        return array_values(
            array_filter(
                $filterValues,
                function ($filterValue) {
                    return ! is_null(filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
                }
            )
        );
    }

    /**
     * Determine if both true and false have been specified.
     *
     * @param array $filterValues
     * @return boolean
     */
    protected function isAllFilterValues($filterValues)
    {
        return in_array(true, $filterValues) && in_array(false, $filterValues);
    }
}
