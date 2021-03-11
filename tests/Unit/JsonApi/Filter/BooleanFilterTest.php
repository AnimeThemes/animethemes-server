<?php

namespace Tests\Unit\JsonApi\Filter;

use App\JsonApi\Filter\BooleanFilter;
use App\JsonApi\QueryParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BooleanFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * If values that are not mappable to booleans are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoBooleans()
    {
        $filter_field = $this->faker->word();

        $boolean_values = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $boolean_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends BooleanFilter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * If both boolean values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllBooleans()
    {
        $filter_field = $this->faker->word();

        $boolean_values = [true, false];

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $boolean_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends BooleanFilter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * The boolean filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsValidatedBoolean()
    {
        $filter_field = $this->faker->word();

        $boolean_value = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $boolean_value ? 'true' : 'false',
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends BooleanFilter {
            // We don't need to do any customization
        };

        $filter_values = $filter->getFilterValues($parser->getConditions($filter_field)[0]);

        $this->assertEquals($boolean_value, $filter_values[0]);
    }
}
