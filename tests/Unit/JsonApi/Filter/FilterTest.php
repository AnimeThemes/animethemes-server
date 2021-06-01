<?php

declare(strict_types=1);

namespace JsonApi\Filter;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FilterTest
 * @package JsonApi\Filter
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
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

        $filterValues = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $filterValues),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
        };

        static::assertFalse($filter->scope($this->faker->word())->shouldApplyFilter($parser->getConditions($filterField)[0]));
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
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

        $filter = new class($parser, $filterField) extends Filter
        {
            // We don't need to do any customization
        };

        $filter->scope($scope);

        static::assertEquals($scope, $filter->getScope());
    }
}
