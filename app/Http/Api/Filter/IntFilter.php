<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

/**
 * Class IntFilter.
 */
abstract class IntFilter extends Filter
{
    /**
     * Convert filter values to integers.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                return filter_var($filterValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are integers.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    return filter_var($filterValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) !== null;
                }
            )
        );
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }
}
