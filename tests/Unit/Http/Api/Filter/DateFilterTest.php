<?php

declare(strict_types=1);

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Http\Api\Filter\DateFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

pest()->use(WithFaker::class);

test('should not apply if no dates', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new DateFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('should not apply if wrong format', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Date::now()->format(DateTimeInterface::RFC1036));

    $filter = new DateFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('should apply if accepted format', function (): void {
    $filterField = fake()->word();

    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Date::now()->format($dateFormat->value));

    $filter = new DateFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeTrue();
});

test('converts dates to canonical format', function (): void {
    $filterField = fake()->word();

    $dateFormat = Arr::random(AllowedDateFormat::cases());

    $dateFilter = Date::now()->format($dateFormat->value);

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $dateFilter);

    $filter = new DateFilter($filterField);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    expect($filterValues[0])->toEqual(DateTime::createFromFormat('!'.$dateFormat->value, $dateFilter)->format(AllowedDateFormat::YMDHISU->value));
});
