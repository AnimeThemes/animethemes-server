<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use DateTime;

class DateFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                foreach (AllowedDateFormat::cases() as $allowedDateFormat) {
                    $date = DateTime::createFromFormat('!'.$allowedDateFormat->value, $filterValue);
                    if ($date && $date->format($allowedDateFormat->value) === $filterValue) {
                        return $date->format(AllowedDateFormat::YMDHISU->value);
                    }
                }

                return null;
            },
            $filterValues
        );
    }

    /**
     * Get only filter values that are valid dates.
     *
     * @param  array  $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    foreach (AllowedDateFormat::cases() as $allowedDateFormat) {
                        $date = DateTime::createFromFormat('!'.$allowedDateFormat->value, $filterValue);
                        if ($date && $date->format($allowedDateFormat->value) === $filterValue) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }

    /**
     * Determine if all valid filter values have been specified.
     * By default, this is false as we assume an unrestricted amount of valid values.
     *
     * @param  array  $filterValues
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
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');
        $dateFormats = implode(',', $allowedDateFormats);

        return [
            'required',
            "date_format:$dateFormats",
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
            ComparisonOperator::LT,
            ComparisonOperator::GT,
            ComparisonOperator::LTE,
            ComparisonOperator::GTE,
        ];
    }
}
