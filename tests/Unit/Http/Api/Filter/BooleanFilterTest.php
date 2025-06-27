<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Scope\GlobalScope;
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
    public function test_should_not_apply_if_no_booleans(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Str::random());

        $filter = new BooleanFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If both boolean values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function test_should_not_apply_if_all_booleans(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, 'true,false');

        $filter = new BooleanFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The boolean filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function test_converts_validated_boolean(): void
    {
        $booleanValue = $this->faker->boolean();

        $filter = new BooleanFilter($this->faker->word());

        $filterValues = $filter->getFilterValues(Arr::wrap($booleanValue ? 'true' : 'false'));

        static::assertEquals($booleanValue, $filterValues[0]);
    }
}
