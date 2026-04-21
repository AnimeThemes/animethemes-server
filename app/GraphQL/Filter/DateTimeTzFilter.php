<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use DateTime;
use Illuminate\Support\Arr;

class DateTimeTzFilter extends Filter
{
    /**
     * Convert filter values if needed. By default, no conversion is needed.
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return Arr::map(
            $filterValues,
            function (string $filterValue): ?string {
                foreach (AllowedDateFormat::cases() as $allowedDateFormat) {
                    $date = DateTime::createFromFormat('!'.$allowedDateFormat->value, $filterValue);
                    if ($date && $date->format($allowedDateFormat->value) === $filterValue) {
                        return $date->format(AllowedDateFormat::YMDHISU->value);
                    }
                }

                return null;
            }
        );
    }

    /**
     * Get the validation rules for the filter.
     */
    protected function getRules(): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');
        $dateFormats = implode(',', $allowedDateFormats);

        return [
            'required',
            "date_format:$dateFormats",
        ];
    }
}
