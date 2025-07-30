<?php

declare(strict_types=1);

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\Scope\GlobalScope;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('should not apply if no floats', function () {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new FloatFilter($filterField);

    $this->assertFalse($criteria->shouldFilter($filter, $criteria->getScope()));
});

test('converts validated floats', function () {
    $filterField = fake()->word();

    $floatValue = fake()->randomFloat();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $floatValue);

    $filter = new FloatFilter($filterField);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
});
