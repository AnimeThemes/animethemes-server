<?php

declare(strict_types=1);

use App\GraphQL\Filter\BooleanFilter;
use Illuminate\Support\Arr;

it('converts validated boolean', function () {
    $booleanValue = fake()->boolean();

    $filter = new BooleanFilter(fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap(Arr::random([$booleanValue, $booleanValue ? 'true' : 'false'])));

    $this->assertEquals($booleanValue, $filterValues[0]);
});
