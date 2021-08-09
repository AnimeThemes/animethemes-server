<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Filter\DateFilter;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Query;
use DateTime;
use DateTimeInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

/**
 * Class DateFilterTest.
 */
class DateFilterTest extends TestCase
{
    use WithFaker;

    /**
     * If values that are not mappable to dates are specified for the key, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfNoDates()
    {
        $filterField = $this->faker->word();

        $dateValues = $this->faker->words($this->faker->randomDigitNotNull());

        $parameters = [
            FilterParser::$param => [
                $filterField => implode(',', $dateValues),
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria));
    }

    /**
     * If values do not use allowed date formats, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfWrongFormat()
    {
        $filterField = $this->faker->word();

        $filterValue = Date::now()->format(DateTimeInterface::RFC1036);

        $parameters = [
            FilterParser::$param => [
                $filterField => $filterValue,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertFalse($filter->shouldApplyFilter($criteria));
    }

    /**
     * If values do use allowed date formats, apply the filter.
     *
     * @return void
     */
    public function testShouldApplyIfAcceptedFormat()
    {
        $filterField = $this->faker->word();

        $filterValue = Date::now()->format(AllowedDateFormat::getRandomValue());

        $parameters = [
            FilterParser::$param => [
                $filterField => $filterValue,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $criteria = $query->getFilterCriteria()->first();

        static::assertTrue($filter->shouldApplyFilter($criteria));
    }

    /**
     * The date filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsDatesToCanonicalFormat()
    {
        $filterField = $this->faker->word();

        $dateFormat = AllowedDateFormat::getRandomValue();

        $dateFilter = Date::now()->format($dateFormat);

        $parameters = [
            FilterParser::$param => [
                $filterField => $dateFilter,
            ],
        ];

        $query = Query::make($parameters);

        $filter = new class($query->getFilterCriteria(), $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($query->getFilterCriteria()->first());

        static::assertEquals(
            DateTime::createFromFormat('!'.$dateFormat, $dateFilter)->format(AllowedDateFormat::YMDHISU),
            $filterValues[0]
        );
    }
}
