<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
    public function testShouldNotApplyIfNoBooleans()
    {
        $filterField = $this->faker->word();

        $booleanValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $booleanValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
    }

    /**
     * If both boolean values are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfAllBooleans()
    {
        $filterField = $this->faker->word();

        $booleanValues = [true, false];

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $booleanValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria, new GlobalScope()));
    }

    /**
     * The boolean filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsValidatedBoolean()
    {
        $filterField = $this->faker->word();

        $booleanValue = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                $filterField => $booleanValue ? 'true' : 'false',
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($filterField) extends BooleanFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        $filterValues = $filter->getFilterValues($criteria);

        static::assertEquals($booleanValue, $filterValues[0]);
    }
}
