<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Rules\Api\IsValidBoolean;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

class BooleanFilter extends Filter
{
    public function getBaseType(): Type
    {
        return Type::boolean();
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    public function convertFilterValues(array $filterValues): array
    {
        return Arr::map(
            $filterValues,
            fn (string $filterValue): ?bool => filter_var($filterValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
        );
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [
            'required',
            new IsValidBoolean(),
        ];
    }
}
