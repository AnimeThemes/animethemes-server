<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('default column', function (): void {
    $filter = new class(fake()->word()) extends Filter
    {
        /**
         * Convert filter values to integers.
         */
        public function convertFilterValues(array $filterValues): array
        {
            return $filterValues;
        }

        /**
         * Get only filter values that are integers.
         */
        public function getValidFilterValues(array $filterValues): array
        {
            return $filterValues;
        }

        /**
         * Determine if all valid filter values have been specified.
         * By default, this is false as we assume an unrestricted amount of valid values.
         */
        public function isAllFilterValues(array $filterValues): bool
        {
            return false;
        }

        /**
         * Get the validation rules for the filter.
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

    $this->assertEquals($filter->getKey(), $filter->getColumn());
});
