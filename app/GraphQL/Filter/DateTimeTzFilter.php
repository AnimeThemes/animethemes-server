<?php

declare(strict_types=1);

namespace App\GraphQL\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use DateTime;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;

class DateTimeTzFilter extends Filter
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
}
