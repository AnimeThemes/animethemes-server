<?php

declare(strict_types=1);

use App\Http\Api\Filter\FloatFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

pest()->use(WithFaker::class);

test('should not apply if no floats', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new FloatFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('converts validated floats', function (): void {
    $filterField = fake()->word();

    $floatValue = fake()->randomFloat();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $floatValue);

    $filter = new FloatFilter($filterField);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    expect($filterValues[0])->toEqualWithDelta($floatValue, 0.0001);
});
