<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;

class FloatFilter extends Filter
{
    /**
     * Convert filter values to floats.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            fn (string $filterValue) => filter_var($filterValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            $filterValues
        );
    }

    /**
     * Get only filter values that are floats.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                fn (string $filterValue) => filter_var($filterValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE) !== null
            )
        );
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
     */
    public function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }

    /**
     * Get the validation rules for the filter.
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            'required',
            'numeric',
        ];
    }

    /**
     * Get the allowed comparison operators for the filter.
     *
     * @return ComparisonOperator[]
     */
    public function getAllowedComparisonOperators(): array
    {
        return [
            ComparisonOperator::EQ,
            ComparisonOperator::NE,
            ComparisonOperator::LT,
            ComparisonOperator::GT,
            ComparisonOperator::LTE,
            ComparisonOperator::GTE,
        ];
    }
}
