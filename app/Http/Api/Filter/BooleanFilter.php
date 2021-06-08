<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

/**
 * Class BooleanFilter.
 */
abstract class BooleanFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
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
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    return filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
                }
            )
        );
    }

    /**
     * Determine if both true and false have been specified.
     *
     * @param array $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return in_array(true, $filterValues) && in_array(false, $filterValues);
    }
}
