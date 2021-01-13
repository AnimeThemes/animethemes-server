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
     * If we don't have a filter, we should not apply a filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfDoesNotHaveFilter()
    {
        $parameters = [];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $this->faker->word()) extends Filter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter());
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

        $this->assertTrue($filter->shouldApplyFilter());
    }
}
