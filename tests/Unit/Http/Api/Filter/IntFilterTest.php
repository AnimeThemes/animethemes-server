<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class IntFilterTest.
 */
class IntFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to integers are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoIntegers()
    {
        $filterField = $this->faker->word();

        $intValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $intValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField) extends IntFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
    }

    /**
     * The int filter shall convert validated integer inputs to integer values.
     *
     * @return void
     */
    public function testConvertsValidatedIntegers()
    {
        $filterField = $this->faker->word();

        $intValue = $this->faker->year();

        $parameters = [
            FilterParser::$param => [
                $filterField => $intValue,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField) extends IntFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($query->getFilterCriteria()->first());

        static::assertEquals(intval($intValue), $filterValues[0]);
    }
}
