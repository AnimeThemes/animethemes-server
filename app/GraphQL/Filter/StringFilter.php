<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use GraphQL\Type\Definition\Type;

class StringFilter extends Filter
{
    public function getBaseType(): Type
    {
        return Type::string();
    }

    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    public function convertFilterValues(array $filterValues): array
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
