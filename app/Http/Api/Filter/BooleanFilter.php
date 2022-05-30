<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Rules\Api\IsValidBoolean;

/**
 * Class BooleanFilter.
 */
class BooleanFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            fn (string $filterValue) => filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid boolean options.
     * Accepted for true: "1", "true", "on" and "yes".
     * Accepted for false: "0", "false", "off" and "no.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                fn (string $filterValue) => filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null
            )
        );
    }

    /**
     * Determine if both true and false have been specified.
     *
     * @param  array  $filterValues
     * @return bool
     */
    public function isAllFilterValues(array $filterValues): bool
    {
        return in_array(true, $filterValues) && in_array(false, $filterValues);
    }

    /**
     * Get the validation rules for the filter.
     *
     * @return array
     */
    public function getRules(): array
    {
        return [
            new IsValidBoolean(),
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
            ComparisonOperator::EQ(),
            ComparisonOperator::NE(),
        ];
    }
}
