<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Filter\TimestampFilter;
use Illuminate\Support\Arr;

it('converts validated timestamps', function () {
    $timestampValue = fake()->dateTime()->getTimestamp();

    $filter = new TimestampFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($timestampValue));

    $this->assertEquals(DateTime::createFromTimestamp($timestampValue)->format(AllowedDateFormat::YMDHIS->value), $filterValues[0]);
});
