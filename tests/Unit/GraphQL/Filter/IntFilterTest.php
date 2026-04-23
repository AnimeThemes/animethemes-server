<?php

declare(strict_types=1);

use App\GraphQL\Filter\IntFilter;
use Illuminate\Support\Arr;

it('converts validated integers', function (): void {
    $intValue = fake()->year();

    $filter = new IntFilter(fake()->word(), fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($intValue));

    $this->assertEquals(intval($intValue), $filterValues[0]);
});
