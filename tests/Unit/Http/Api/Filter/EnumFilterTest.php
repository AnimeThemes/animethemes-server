<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;
use Tests\Unit\Enums\LocalizedEnum;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

class EnumFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to enum instances are specified for the key, don't apply the filter.
     */
    public function testShouldNotApplyIfNoEnums(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new EnumFilter($filterField, LocalizedEnum::class);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If all enum values are specified for the key, don't apply the filter.
     */
    public function testShouldNotApplyIfAllEnums(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make(
            new GlobalScope(),
            $filterField,
            array_column(LocalizedEnum::cases(), 'value')
        );

        $filter = new EnumFilter($filterField, LocalizedEnum::class);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The enum filter shall convert enum keys to enum values.
     */
    public function testEnumKeyConvertedToValue(): void
    {
        $filterField = $this->faker->word();

        $enum = Arr::random(LocalizedEnum::cases());

        $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $enum->localize());

        $filter = new EnumFilter($filterField, LocalizedEnum::class);

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEquals($enum->value, $filterValues[0]);
    }
}
