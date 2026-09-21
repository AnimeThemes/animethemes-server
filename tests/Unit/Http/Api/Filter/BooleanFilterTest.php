<?php

declare(strict_types=1);

use App\Http\Api\Filter\BooleanFilter;
use App\Http\Api\Scope\GlobalScope;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\Unit\Http\Api\Criteria\Filter\FakeCriteria;

pest()->use(WithFaker::class);

test('should not apply if no booleans', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, Str::random());

    $filter = new BooleanFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('should not apply if all booleans', function (): void {
    $filterField = fake()->word();

    $criteria = FakeCriteria::make(new GlobalScope(), $filterField, 'true,false');

    $filter = new BooleanFilter($filterField);

    expect($criteria->shouldFilter($filter, $criteria->getScope()))->toBeFalse();
});

test('converts validated boolean', function (): void {
    $booleanValue = fake()->boolean();

    $filter = new BooleanFilter(fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($booleanValue ? 'true' : 'false'));

    expect($filterValues[0])->toEqual($booleanValue);
});
