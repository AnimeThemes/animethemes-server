<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use DateTime;
use Illuminate\Support\Arr;

class TimestampFilter extends Filter
{
    /**
     * Convert filter values if needed.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return Arr::map(
            $filterValues,
            fn (string $filterValue): string => DateTime::createFromTimestamp((int) $filterValue)->format(AllowedDateFormat::YMDHIS->value)
        );
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        return [
            'required',
            'numeric',
        ];
    }
}
