<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

class IntFilter extends Filter
{
    public function getBaseType(): Type
    {
        return Type::int();
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    public function convertFilterValues(array $filterValues): array
    {
        return Arr::map(
            $filterValues,
            fn (string $filterValue): ?int => filter_var($filterValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
        );
    }
}
