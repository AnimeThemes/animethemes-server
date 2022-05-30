<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;

/**
 * Class StringFilter.
 */
class StringFilter extends Filter
{
    /**
     * Convert filter values to strings.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return $filterValues;
    }

    /**
     * Get only filter values that are strings.
     *
     * @param  array  $filterValues
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
     * @param  array  $filterValues
     * @return bool
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
            'string',
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
            ComparisonOperator::LIKE(),
            ComparisonOperator::NOTLIKE(),
        ];
    }
}
