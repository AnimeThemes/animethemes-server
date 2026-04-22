<?php

declare(strict_types=1);

use App\GraphQL\Filter\FloatFilter;
use Illuminate\Support\Arr;

it('converts validated floats', function () {
    $floatValue = fake()->randomFloat();

    $filter = new FloatFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($floatValue));

    $this->assertEqualsWithDelta($floatValue, $filterValues[0], 0.0001);
});
