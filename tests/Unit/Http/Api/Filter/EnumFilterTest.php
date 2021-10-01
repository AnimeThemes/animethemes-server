<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Enums\BaseEnum;
use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $enumValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $enumValues),
            ],
        ];

        $query = Query::make($parameters);

        $enum = new class($this->faker->numberBetween(0, 2)) extends BaseEnum
        {
            public const ZERO = 0;
            public const ONE = 1;
            public const TWO = 2;
        };

        $filter = new class($filterField, get_class($enum)) extends EnumFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
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

        $enumValues = $enum::getValues();

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $enumValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField, get_class($enum)) extends EnumFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
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

        $parameters = [
            FilterParser::$param => [
                $filterField => $enum->description,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField, get_class($enum)) extends EnumFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($query->getFilterCriteria()->first());

        static::assertEquals($enum->value, $filterValues[0]);
    }
}
