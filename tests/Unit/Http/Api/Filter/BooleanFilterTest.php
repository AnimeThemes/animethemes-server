<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\BooleanFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

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
    public function testShouldNotApplyIfNoBooleans(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make($filterField, Str::random());

        $filter = new BooleanFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If both boolean values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllBooleans(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make($filterField, 'true,false');

        $filter = new BooleanFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The boolean filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsValidatedBoolean(): void
    {
        $booleanValue = $this->faker->boolean();

        $filter = new BooleanFilter($this->faker->word());

        $filterValues = $filter->getFilterValues(Arr::wrap($booleanValue ? 'true' : 'false'));

        static::assertEquals($booleanValue, $filterValues[0]);
    }
}
