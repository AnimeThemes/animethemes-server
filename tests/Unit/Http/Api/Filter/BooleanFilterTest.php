<?php

declare(strict_types=1);

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('should not apply if no booleans', function () {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Str::random());

    $filter = new BooleanFilter($filterField);

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('should not apply if all booleans', function () {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, 'true,false');

    $filter = new BooleanFilter($filterField);

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('converts validated boolean', function () {
    $booleanValue = fake()->boolean();

    $filter = new BooleanFilter(fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($booleanValue ? 'true' : 'false'));

    $this->assertEquals($booleanValue, $filterValues[0]);
});
