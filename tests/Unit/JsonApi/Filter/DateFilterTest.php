<?php

namespace Tests\Unit\JsonApi\Filter;

use App\Enums\Filter\AllowedDateFormat;
use App\JsonApi\Filter\DateFilter;
use App\JsonApi\QueryParser;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

        $dateValues = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => implode(',', $dateValues),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
    }

    /**
     * If values do not use allowed date formats, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfWrongFormat()
    {
        $filterField = $this->faker->word();

        $filterValue = Carbon::now()->format(DateTimeInterface::RFC1036);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $filterValue,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
    }

    /**
     * If values do use allowed date formats, apply the filter.
     *
     * @return void
     */
    public function testShouldApplyIfAcceptedFormat()
    {
        $filterField = $this->faker->word();

        $filterValue = Carbon::now()->format(AllowedDateFormat::getRandomValue());

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $filterValue,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertTrue($filter->shouldApplyFilter($parser->getConditions($filterField)[0]));
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

        $dateFilter = Carbon::now()->format($dateFormat);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filterField => $dateFilter,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filterField) extends DateFilter
        {
            // We don't need to do any customization
        };

        $filterValues = $filter->getFilterValues($parser->getConditions($filterField)[0]);

        $this->assertEquals(DateTime::createFromFormat('!'.$dateFormat, $dateFilter)->format(AllowedDateFormat::WITH_MICRO), $filterValues[0]);
    }
}
