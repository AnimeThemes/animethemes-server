<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\Filter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
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
    public function testDefaultColumn()
    {
        $filterField = $this->faker->word();

        $parameters = [];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        static::assertEquals($filter->getKey(), $filter->getColumn());
    }

    /**
     * We have a filter if a value is explicitly set for its key.
     *
     * @return void
     */
    public function testShouldApplyIfHasFilter()
    {
        $filterField = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                $filterField => $this->faker->word(),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertTrue($filter->shouldApplyFilter($criteria));
    }

    /**
     * Do not apply the filter if the scope is specified and does not match the filter scope.
     *
     * @return void
     */
    public function testShouldNotApplyIfScopeDoesNotMatch()
    {
        $scope = $this->faker->word();
        $filterField = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                $scope => [
                    $filterField => $this->faker->word(),
                ],
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->scope($this->faker->word())->shouldApplyFilter($criteria));
    }

    /**
     * Apply the filter if the scope is specified and matches the filter scope.
     *
     * @return void
     */
    public function testShouldApplyIfScopeMatches()
    {
        $scope = $this->faker->word();
        $filterField = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                $scope => [
                    $filterField => $this->faker->word(),
                ],
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertTrue($filter->scope($scope)->shouldApplyFilter($criteria));
    }

    /**
     * By default, the scope of the filter shall be global.
     *
     * @return void
     */
    public function testDefaultScope()
    {
        $filterField = $this->faker->word();

        $parameters = [];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        static::assertEmpty($filter->getScope());
    }

    /**
     * The filter's scope shall be modifiable.
     *
     * @return void
     */
    public function testSetScope()
    {
        $scope = $this->faker->word();
        $filterField = $this->faker->word();

        $parameters = [];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends Filter
        {
            /**
             * Convert filter values to integers.
             *
             * @param array $filterValues
             * @return array
             */
            protected function convertFilterValues(array $filterValues): array
            {
                return $filterValues;
            }

            /**
             * Get only filter values that are integers.
             *
             * @param array $filterValues
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
             * @param array $filterValues
             * @return bool
             */
            protected function isAllFilterValues(array $filterValues): bool
            {
                return false;
            }
        };

        $filter->scope($scope);

        static::assertEquals($scope, $filter->getScope());
    }
}
