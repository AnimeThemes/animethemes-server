<?php

namespace App\JsonApi\Filter;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class DateFilter extends Filter
{
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
                    try {
                        Validator::make(
                            ['filterValue' => $filterValue],
                            ['filterValue' => 'date']
                        )
                        ->validate();

                        return true;
                    } catch (ValidationException $e) {
                        return false;
                    }
                }
            )
        );
    }
}
