<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

/**
 * Class IntFilter.
 */
abstract class FloatFilter extends Filter
{
    /**
     * Convert filter values to floats.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                return filter_var($filterValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are floats.
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
                    return filter_var($filterValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null;
                }
            )
        );
    }
}
