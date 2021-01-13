<?php

namespace Tests\Unit\JsonApi\Filter;

use App\JsonApi\Filter\EnumFilter;
use App\JsonApi\QueryParser;
use BenSampo\Enum\Enum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnumFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * If values that are not mappable to enum instances are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoEnums()
    {
        $filter_field = $this->faker->word();

        $enum_values = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $enum_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $enum = new class($this->faker->numberBetween(0, 2)) extends Enum {
            const ZERO = 0;
            const ONE = 1;
            const TWO = 2;
        };

        $filter = new class($parser, $filter_field, get_class($enum)) extends EnumFilter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter());
    }

    /**
     * If all enum values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllEnums()
    {
        $filter_field = $this->faker->word();

        $enum = new class($this->faker->numberBetween(0, 2)) extends Enum {
            const ZERO = 0;
            const ONE = 1;
            const TWO = 2;
        };

        $enum_class = get_class($enum);

        $enum_values = $enum_class::getValues();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $enum_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field, get_class($enum)) extends EnumFilter {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter());
    }
}
