<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Rules\Api\IsValidBoolean;

class BooleanFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            fn (string $filterValue): ?bool => filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid boolean options.
     * Accepted for true: "1", "true", "on" and "yes".
     * Accepted for false: "0", "false", "off" and "no.
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                fn (string $filterValue): bool => filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null
            )
        );
    }

    /**
     * Determine if both true and false have been specified.
     */
    public function isAllFilterValues(array $filterValues): bool
    {
        return in_array(true, $filterValues) && in_array(false, $filterValues);
    }

    /**
     * Get the validation rules for the filter.
     */
    public function getRules(): array
    {
        return [
            'required',
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
            ComparisonOperator::EQ,
            ComparisonOperator::NE,
        ];
    }
}
