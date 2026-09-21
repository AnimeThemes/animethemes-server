<?php

declare(strict_types=1);

use App\GraphQL\Filter\BooleanFilter;
use Illuminate\Support\Arr;

it('converts validated boolean', function (): void {
    $booleanValue = fake()->boolean();

    $filter = new BooleanFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap(Arr::random([$booleanValue, $booleanValue ? 'true' : 'false'])));

    expect($filterValues[0])->toEqual($booleanValue);
});
