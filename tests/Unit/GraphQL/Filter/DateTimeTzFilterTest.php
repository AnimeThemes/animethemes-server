<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Filter\DateTimeTzFilter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Tests\Unit\GraphQL\Criteria\Filter\FakeCriteria;

it('converts dates to canonical format', function () {
    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $dateFilter = Date::now()->format($dateFormat->value);

    $filter = new DateTimeTzFilter(fake()->word());

    $criteria = new FakeCriteria($filter, $dateFilter);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEquals(
        DateTime::createFromFormat('!'.$dateFormat->value, $dateFilter)->format(AllowedDateFormat::YMDHISU->value),
        $filterValues[0]
    );
});
