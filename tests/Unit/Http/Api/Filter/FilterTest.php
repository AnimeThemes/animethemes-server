<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Filter\Filter;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FilterTest.
 */
class FilterTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the filter column shall be derived from the filter key.
     *
     * @return void
     */
    public function test_default_column(): void
    {
        $filter = new class($this->faker->word()) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param  array  $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param  array  $filterValues
             * @return array
             */
            protected function getValidFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Determine if all valid filter values have been specified.
             * By default, this is false as we assume an unrestricted amount of valid values.
             *
             * @param  array  $filterValues
             * @return bool
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
    }
}
