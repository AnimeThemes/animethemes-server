<?php

declare(strict_types=1);

use App\Http\Api\Filter\EnumFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\Unit\Enums\LocalizedEnum;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(WithFaker::class);

test('should not apply if no enums', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new EnumFilter($filterField, LocalizedEnum::class);

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should not apply if all enums', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(
        new GlobalScope(),
        $filterField,
        array_column(LocalizedEnum::cases(), 'value')
    );

    $filter = new EnumFilter($filterField, LocalizedEnum::class);

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('enum key converted to value', function (): void {
    $filterField = fake()->word();

    $enum = Arr::random(LocalizedEnum::cases());

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $enum->localize());

    $filter = new EnumFilter($filterField, LocalizedEnum::class);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEquals($enum->value, $filterValues[0]);
});
