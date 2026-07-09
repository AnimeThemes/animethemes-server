<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use Illuminate\Support\Arr;

class FuzzyDateFilter extends Filter
{
    /**
     * Convert filter values if needed.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return Arr::map(
            $filterValues,
            fn (string|int $filterValue): ?int => filter_var($filterValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE),
        );
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [
            'required',
            'integer',
            'min:0',
            'max:99991231',
        ];
    }
}
