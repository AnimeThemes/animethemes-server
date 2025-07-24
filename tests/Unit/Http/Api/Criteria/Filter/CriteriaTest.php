<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Filter;

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * If the criteria and filter keys do not match, the filter should not be applied.
     */
    public function testShouldNotFilterIfKeyMismatch(): void
    {
        $expression = new Expression($this->faker->word());
        $comparisonOperator = Arr::random(ComparisonOperator::cases());
        $predicate = new Predicate($this->faker->word(), $comparisonOperator, $expression);
        $scope = new GlobalScope();
        $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

        $criteria = new FakeCriteria($predicate, $logicalOperator, $scope);

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

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     */
    public function testShouldFilterIfKeyMatch(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $comparisonOperator = Arr::random(ComparisonOperator::cases());
        $predicate = new Predicate($key, $comparisonOperator, $expression);
        $scope = new GlobalScope();
        $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

        $criteria = new FakeCriteria($predicate, $logicalOperator, $scope);

        $filter = new class($key) extends Filter
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

        static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     */
    public function testShouldNotFilterIfNotWithinScope(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $comparisonOperator = Arr::random(ComparisonOperator::cases());
        $predicate = new Predicate($key, $comparisonOperator, $expression);
        $scope = new TypeScope($this->faker->word());
        $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

        $criteria = new FakeCriteria($predicate, $logicalOperator, $scope);

        $filter = new class($key) extends Filter
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

        static::assertFalse($criteria->shouldFilter($filter, new GlobalScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     */
    public function testShouldFilterIfWithinScope(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $comparisonOperator = Arr::random(ComparisonOperator::cases());
        $predicate = new Predicate($key, $comparisonOperator, $expression);
        $scope = new TypeScope(Str::of(Str::random())->lower()->singular()->__toString());
        $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

        $criteria = new FakeCriteria($predicate, $logicalOperator, $scope);

        $filter = new class($key) extends Filter
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

        static::assertTrue($criteria->shouldFilter($filter, $scope));
    }
}
