<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Filter\TimestampFilter;
use Illuminate\Support\Arr;

it('converts validated timestamps', function (): void {
    $timestampValue = fake()->dateTime()->getTimestamp();

    $filter = new TimestampFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($timestampValue));

    expect($filterValues[0])->toEqual(DateTime::createFromTimestamp($timestampValue)->format(AllowedDateFormat::YMDHIS->value));
});
