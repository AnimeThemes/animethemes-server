<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\QueryParser;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class IntFilterTest.
 */
class IntFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to integers are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoIntegers()
    {
        $filterField = $this->faker->word();

        $intValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $intValues),
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends IntFilter
        {
            // We don't need to do any customization
        };

        static::assertFalse($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
    }

    /**
     * The boolean filter shall convert validated integer inputs to integer values.
     *
     * @return void
     */
    public function testConvertsValidatedIntegers()
    {
        $filterField = $this->faker->word();

        $intValue = $this->faker->year();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $intValue,
            ],
        ];

        $parser = QueryParser::make($parameters);

        $filter = new class($parser, $filterField) extends IntFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($parser->getConditions($filterField)[0]);

        static::assertEquals(intval($intValue), $filterValues[0]);
    }
}
