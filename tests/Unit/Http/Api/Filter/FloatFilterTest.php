<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

class FloatFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to floats are specified for the key, don't apply the filter.
     */
    public function testShouldNotApplyIfNoFloats(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new FloatFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The float filter shall convert validated float inputs to float values.
     */
    public function testConvertsValidatedFloats(): void
    {
        $filterField = $this->faker->word();

        $floatValue = $this->faker->randomFloat();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $floatValue);

        $filter = new FloatFilter($filterField);

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
    }
}
