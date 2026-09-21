<?php

declare(strict_types=1);

use App\GraphQL\Filter\FloatFilter;
use Illuminate\Support\Arr;

it('converts validated floats', function (): void {
    $floatValue = fake()->randomFloat();

    $filter = new FloatFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($floatValue));

    expect($filterValues[0])->toEqualWithDelta($floatValue, 0.0001);
});
