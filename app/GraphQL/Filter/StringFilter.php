<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

class StringFilter extends Filter
{
    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return $filterValues;
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [
            'required',
            'string',
        ];
    }
}
