<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Filter;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Filter\DateFilter;
use DateTime;
use DateTimeInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

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
    public function testShouldNotApplyIfNoDates(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make($filterField, $this->faker->words($this->faker->randomDigitNotNull()));

        $filter = new DateFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If values do not use allowed date formats, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfWrongFormat(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make($filterField, Date::now()->format(DateTimeInterface::RFC1036));

        $filter = new DateFilter($filterField);

        static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * If values do use allowed date formats, apply the filter.
     *
     * @return void
     */
    public function testShouldApplyIfAcceptedFormat(): void
    {
        $filterField = $this->faker->word();

        $criteria = FakeCriteria::make($filterField, Date::now()->format(AllowedDateFormat::getRandomValue()));

        $filter = new DateFilter($filterField);

        static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
    }

    /**
     * The date filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsDatesToCanonicalFormat(): void
    {
        $filterField = $this->faker->word();

        $dateFormat = AllowedDateFormat::getRandomValue();

        $dateFilter = Date::now()->format($dateFormat);

        $criteria = FakeCriteria::make($filterField, $dateFilter);

        $filter = new DateFilter($filterField);

        $filterValues = $filter->getFilterValues($criteria->getFilterValues());

        static::assertEquals(
            DateTime::createFromFormat('!'.$dateFormat, $dateFilter)->format(AllowedDateFormat::YMDHISU),
            $filterValues[0]
        );
    }
}
