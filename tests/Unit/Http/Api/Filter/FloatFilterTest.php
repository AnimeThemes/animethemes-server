<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FloatFilterTest.
 */
class FloatFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to floats are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoFloats()
    {
        $filterField = $this->faker->word();

        $floatValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $floatValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends FloatFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
    }

    /**
     * The float filter shall convert validated float inputs to float values.
     *
     * @return void
     */
    public function testConvertsValidatedFloats()
    {
        $filterField = $this->faker->word();

        $floatValue = $this->faker->randomFloat();

        $parameters = [
            FilterParser::$param => [
                $filterField => $floatValue,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends FloatFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($query->getFilterCriteria()->first());

        static::assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
    }
}
