<?php

namespace App\JsonApi\Filter;

use App\Enums\Filter\AllowedDateFormat;
use DateTime;

abstract class DateFilter extends Filter
{
    /**
     * Convert filter values to booleans.
     *
     * @param array $filterValues
     * @return array
     */
    protected function convertFilterValues(array $filterValues)
    {
        return array_map(
            function ($filterValue) {
                foreach (AllowedDateFormat::getValues() as $allowedDateFormat) {
                    $date = DateTime::createFromFormat('!'.$allowedDateFormat, $filterValue);
                    if ($date && $date->format($allowedDateFormat) == $filterValue) {
                        return $date->format(AllowedDateFormat::WITH_MICRO);
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
    protected function getValidFilterValues(array $filterValues)
    {
        return array_values(
            array_filter(
                $filterValues,
                function ($filterValue) {
                    foreach (AllowedDateFormat::getValues() as $allowedDateFormat) {
                        $date = DateTime::createFromFormat('!'.$allowedDateFormat, $filterValue);
                        if ($date && $date->format($allowedDateFormat) == $filterValue) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }
}
