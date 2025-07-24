<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

class IntFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to integers are specified for the key, don't apply the filter.
     */
    public function testShouldNotApplyIfNoIntegers(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new IntFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The int filter shall convert validated integer inputs to integer values.
     */
    public function testConvertsValidatedIntegers(): void
    {
        $filterField = $this->faker->word();

        $intValue = $this->faker->year();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $intValue);

        $filter = new IntFilter($filterField);

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEquals(intval($intValue), $filterValues[0]);
    }
}
