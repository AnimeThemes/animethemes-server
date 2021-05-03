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
        $filter_field = $this->faker->word();

        $date_values = $this->faker->words($this->faker->randomDigitNotNull);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => implode(',', $date_values),
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * If values do not use allowed date formats, don't apply the filter.
     *
     * @return void
     */
    public function testShouldNotApplyIfWrongFormat()
    {
        $filter_field = $this->faker->word();

        $filter_value = Carbon::now()->format(DateTimeInterface::RFC1036);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $filter_value,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertFalse($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * If values do use allowed date formats, apply the filter.
     *
     * @return void
     */
    public function testShouldApplyIfAcceptedFormat()
    {
        $filter_field = $this->faker->word();

        $filter_value = Carbon::now()->format(AllowedDateFormat::getRandomValue());

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $filter_value,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends DateFilter
        {
            // We don't need to do any customization
        };

        $this->assertTrue($filter->shouldApplyFilter($parser->getConditions($filter_field)[0]));
    }

    /**
     * The date filter shall convert validated boolean options to boolean values.
     *
     * @return void
     */
    public function testConvertsDatesToCanonicalFormat()
    {
        $filter_field = $this->faker->word();

        $date_format = AllowedDateFormat::getRandomValue();

        $date_filter = Carbon::now()->format($date_format);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                $filter_field => $date_filter,
            ],
        ];

        $parser = new QueryParser($parameters);

        $filter = new class($parser, $filter_field) extends DateFilter
        {
            // We don't need to do any customization
        };

        $filter_values = $filter->getFilterValues($parser->getConditions($filter_field)[0]);

        $this->assertEquals(DateTime::createFromFormat('!'.$date_format, $date_filter)->format(AllowedDateFormat::WITH_MICRO), $filter_values[0]);
    }
}
