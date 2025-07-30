<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default column', function () {
    $filter = new class(fake()->word()) extends Filter
    {
        /**
         * Convert filter values to integers.
         *
         * @param  array  $filterValues
         * @return array
         */
        public function convertFilterValues(array $filterValues): array
        {
            return $filterValues;
        }

        /**
         * Get only filter values that are integers.
         *
         * @param  array  $filterValues
         * @return array
         */
        public function getValidFilterValues(array $filterValues): array
        {
            return $filterValues;
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
            return [];
        }

        /**
         * Get the allowed comparison operators for the filter.
         *
         * @return ComparisonOperator[]
         */
        public function getAllowedComparisonOperators(): array
        {
            return [];
        }
    };

    static::assertEquals($filter->getKey(), $filter->getColumn());
});
