<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Filter\DateTimeTzFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

it('converts dates to canonical format', function () {
    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $dateFilter = Date::now()->format($dateFormat->value);

    $filter = new DateTimeTzFilter(fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($dateFilter));

    $this->assertEquals(
        DateTime::createFromFormat('!'.$dateFormat->value, $dateFilter)->format(AllowedDateFormat::YMDHISU->value),
        $filterValues[0]
    );
});
