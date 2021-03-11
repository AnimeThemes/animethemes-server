<?php

namespace Tests\Unit\JsonApi\Filter;

use App\JsonApi\Filter\Filter;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The filter key shall be accessible.
     *
     * @return void
     */
    public function testGetKey()
    {
        $filter_field = $this->faker->word();

        $parameters = [];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $this->assertEquals($filter_field, $filter->getKey());
    }

    /**
     * The filter values shall be accessible through conditions.
     *
     * @return void
     */
    public function testGetValues()
    {
        $filter_field = $this->faker->word();

        $filter_values = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $filter_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $conditions = collect($filter->getConditions());

        $this->assertEmpty(array_diff($filter_values, $filter->getFilterValues($conditions->first())));
    }

    /**
     * We have a filter if a value is explicitly set for its key.
     *
     * @return void
     */
    public function testShouldApplyIfHasFilter()
    {
        $filter_field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $this->faker->word(),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $this->assertTrue($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * Do not apply the filter if the scope is specified and does not match the filter scope.
     *
     * @return void
     */
    public function testShouldNotApplyIfScopeDoesNotMatch()
    {
        $scope = $this->faker->word();
        $filter_field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $scope => [
                    $filter_field => $this->faker->word(),
                ],
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->scope($this->faker->word())->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * Apply the filter if the scope is specified and matches the filter scope.
     *
     * @return void
     */
    public function testShouldApplyIfScopeMatches()
    {
        $scope = $this->faker->word();
        $filter_field = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $scope => [
                    $filter_field => $this->faker->word(),
                ],
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $this->assertTrue($filter->scope($scope)->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * By default, the scope of the filter shall be global.
     *
     * @return void
     */
    public function testDefaultScope()
    {
        $filter_field = $this->faker->word();

        $parameters = [];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $this->assertEmpty($filter->getScope());
    }

    /**
     * The filter's scope shall be modifiable.
     *
     * @return void
     */
    public function testSetScope()
    {
        $scope = $this->faker->word();
        $filter_field = $this->faker->word();

        $parameters = [];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends Filter {
            // We don't need to do any customization
        };

        $scoped_filter = $filter->scope($scope);

        $this->assertEquals($scope, $filter->getScope());
    }
}
