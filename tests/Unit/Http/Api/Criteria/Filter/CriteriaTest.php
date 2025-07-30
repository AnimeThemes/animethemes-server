<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\BinaryLogicalOperator;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Criteria\Filter\Expression;
use App\Http\Api\Criteria\Filter\Predicate;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Scope\GlobalScope;
use App\Http\Api\Scope\TypeScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('should not filter if key mismatch', function () {
    $expression = new Expression(fake()->word());
    $comparisonOperator = Arr::random(ComparisonOperator::cases());
    $predicate = new Predicate(fake()->word(), $comparisonOperator, $expression);
    $scope = new GlobalScope();
    $logicalOperator = Arr::random(BinaryLogicalOperator::cases());

    $criteria = new FakeCriteria($predicate, $logicalOperator, $scope);

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

    static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should filter if key match', function () {
    $key = fake()->word();

    $expression = new Expression(fake()->word());
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

    static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should not filter if not within scope', function () {
    $key = fake()->word();

    $expression = new Expression(fake()->word());
    $comparisonOperator = Arr::random(ComparisonOperator::cases());
    $predicate = new Predicate($key, $comparisonOperator, $expression);
    $scope = new TypeScope(fake()->word());
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

    static::assertFalse($criteria->shouldFilter($filter, new GlobalScope()));
});

test('should filter if within scope', function () {
    $key = fake()->word();

    $expression = new Expression(fake()->word());
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

    static::assertTrue($criteria->shouldFilter($filter, $scope));
});
