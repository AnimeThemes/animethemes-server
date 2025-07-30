<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Filter\DateFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('should not apply if no dates', function () {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new DateFilter($filterField);

    static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should not apply if wrong format', function () {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Date::now()->format(DateTimeInterface::RFC1036));

    $filter = new DateFilter($filterField);

    static::assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should apply if accepted format', function () {
    $filterField = fake()->word();

    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Date::now()->format($dateFormat->value));

    $filter = new DateFilter($filterField);

    static::assertTrue($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('converts dates to canonical format', function () {
    $filterField = fake()->word();

    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $dateFilter = Date::now()->format($dateFormat->value);

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $dateFilter);

    $filter = new DateFilter($filterField);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    static::assertEquals(
        DateTime::createFromFormat('!'.$dateFormat->value, $dateFilter)->format(AllowedDateFormat::YMDHISU->value),
        $filterValues[0]
    );
});
