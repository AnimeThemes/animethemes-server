<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\Filter;
use App\Http\Api\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FilterTest.
 */
class FilterTest extends TestCase
{
    use WithFaker;

    /**
     * The filter key shall be accessible.
     *
     * @return void
     */
    public function testGetKey()
    {
        $filterField = $this->faker->word();

        $parameters = [];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        static::assertEquals($filterField, $filter->getKey());
    }

    /**
     * The filter values shall be accessible through conditions.
     *
     * @return void
     */
    public function testGetValues()
    {
        $filterField = $this->faker->word();

        $filterValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $filterValues),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        $conditions = collect($filter->getConditions());

        static::assertEmpty(array_diff($filterValues, $filter->getFilterValues($conditions->first())));
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
            QueryParser::PARAM_FILTER => [
                $filterField => $this->faker->word(),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        static::assertTrue($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
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
            QueryParser::PARAM_FILTER => [
                $scope => [
                    $filterField => $this->faker->word(),
                ],
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        $condition = $parser->getConditions($filterField)[0];

        static::assertFalse($filter->scope($this->faker->word())->shouldApplyFilter($condition));
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
            QueryParser::PARAM_FILTER => [
                $scope => [
                    $filterField => $this->faker->word(),
                ],
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        static::assertTrue($filter->scope($scope)->shouldApplyFilter($parser->getConditions($filterField)[0]));
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

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter {
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
