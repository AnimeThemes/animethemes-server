<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\FloatFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

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

        $criteria = FakeCriteria::make($filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new FloatFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
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

        $criteria = FakeCriteria::make($filterField, $floatValue);

        $filter = new FloatFilter($filterField);

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
    }
}
