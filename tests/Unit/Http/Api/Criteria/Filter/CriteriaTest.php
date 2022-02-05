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
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class CriteriaTest.
 */
class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * If the criteria and filter keys do not match, the filter should not be applied.
     *
     * @return void
     */
    public function testShouldNotFilterIfKeyMismatch(): void
    {
        $expression = new Expression($this->faker->word());
        $predicate = new Predicate($this->faker->word(), ComparisonOperator::getRandomInstance(), $expression);
        $scope = new GlobalScope();

        $criteria = new FakeCriteria($predicate, BinaryLogicalOperator::getRandomInstance(), $scope);

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
        };

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     *
     * @return void
     */
    public function testShouldFilterIfKeyMatch(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $predicate = new Predicate($key, ComparisonOperator::getRandomInstance(), $expression);
        $scope = new GlobalScope();

        $criteria = new FakeCriteria($predicate, BinaryLogicalOperator::getRandomInstance(), $scope);

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
             * @return bool
             */
            public function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     *
     * @return void
     */
    public function testShouldNotFilterIfNotWithinScope(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $predicate = new Predicate($key, ComparisonOperator::getRandomInstance(), $expression);
        $scope = new TypeScope($this->faker->word());

        $criteria = new FakeCriteria($predicate, BinaryLogicalOperator::getRandomInstance(), $scope);

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
             * @return bool
             */
            public function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        static::assertFalse($criteria->shouldFilter($filter, new GlobalScope()));
    }

    /**
     * If the criteria and filter keys match, the filter should be applied.
     *
     * @return void
     */
    public function testShouldFilterIfWithinScope(): void
    {
        $key = $this->faker->word();

        $expression = new Expression($this->faker->word());
        $predicate = new Predicate($key, ComparisonOperator::getRandomInstance(), $expression);
        $scope = new TypeScope(Str::of(Str::random())->lower()->singular()->__toString());

        $criteria = new FakeCriteria($predicate, BinaryLogicalOperator::getRandomInstance(), $scope);

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
             * @return bool
             */
            public function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        static::assertTrue($criteria->shouldFilter($filter, $scope));
    }
}
