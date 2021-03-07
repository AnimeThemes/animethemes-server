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
     * The filter values shall be accessible.
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
     * The filter key shall be accessible.
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

        $this->assertEquals($filter_values, $filter->getFilterValues($conditions->first()));
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
}
