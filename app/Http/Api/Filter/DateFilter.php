<?php

declare(strict_types=1);

namespace App\Http\Api\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use DateTime;

/**
 * Class DateFilter.
 */
abstract class DateFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues): array
    {
        return array_map(
            function (string $filterValue) {
                foreach (AllowedDateFormat::getValues() as $allowedDateFormat) {
                    $date = DateTime::createFromFormat('!'.$allowedDateFormat, $filterValue);
                    if ($date && $date->format($allowedDateFormat) === $filterValue) {
                        return $date->format(AllowedDateFormat::YMDHISU);
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
     * @param array $filterValues
     * @return array
     */
    protected function getValidFilterValues(array $filterValues): array
    {
        return array_values(
            array_filter(
                $filterValues,
                function (string $filterValue) {
                    foreach (AllowedDateFormat::getValues() as $allowedDateFormat) {
                        $date = DateTime::createFromFormat('!'.$allowedDateFormat, $filterValue);
                        if ($date && $date->format($allowedDateFormat) === $filterValue) {
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
     * @param array $filterValues
     * @return bool
     */
    protected function isAllFilterValues(array $filterValues): bool
    {
        return false;
    }
}
