<?php

declare(strict_types=1);

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

pest()->use(WithFaker::class);

test('should not apply if no integers', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, fake()->words(fake()->randomDigitNotNull()));

    $filter = new IntFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('converts validated integers', function (): void {
    $filterField = fake()->word();

    $intValue = fake()->year();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, $intValue);

    $filter = new IntFilter($filterField);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    expect($filterValues[0])->toEqual(intval($intValue));
});
