<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Enums\BaseEnum;
use App\Http\Api\Filter\EnumFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

/**
 * Class EnumFilterTest.
 */
class EnumFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to enum instances are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoEnums()
    {
        $filterField = $this->faker->word();

        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $criteria = FakeCriteria::make($filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new EnumFilter($filterField, get_class($enum));

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If all enum values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllEnums()
    {
        $filterField = $this->faker->word();

        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $criteria = FakeCriteria::make($filterField, $enum::getValues());

        $filter = new EnumFilter($filterField, get_class($enum));

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The enum filter shall convert enum keys to enum values.
     *
     * @return void
     */
    public function testEnumKeyConvertedToValue()
    {
        $filterField = $this->faker->word();

        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $criteria = FakeCriteria::make($filterField, $enum->description);

        $filter = new EnumFilter($filterField, get_class($enum));

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEquals($enum->value, $filterValues[0]);
    }
}
