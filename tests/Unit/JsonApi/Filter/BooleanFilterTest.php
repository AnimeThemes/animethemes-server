<?php

declare(strict_types=1);

namespace JsonApi\Filter;

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class BooleanFilterTest.
 */
class BooleanFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to booleans are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoBooleans()
    {
        $filterField = $this->faker->word();

        $booleanValues = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $booleanValues),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        static::assertFalse($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
    }

    /**
     * If both boolean values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllBooleans()
    {
        $filterField = $this->faker->word();

        $booleanValues = [true, false];

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $booleanValues),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        static::assertFalse($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
    }

    /**
     * The boolean filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsValidatedBoolean()
    {
        $filterField = $this->faker->word();

        $booleanValue = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $booleanValue ? 'true' : 'false',
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($parser->getConditions($filterField)[0]);

        static::assertEquals($booleanValue, $filterValues[0]);
    }
}
