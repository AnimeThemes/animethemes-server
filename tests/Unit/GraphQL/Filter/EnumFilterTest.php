<?php

declare(strict_types=1);

use App\GraphQL\Filter\EnumFilter;
use Illuminate\Support\Arr;
use Tests\Unit\Enums\LocalizedEnum;

test('enum converted to value', function () {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class, fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($enum));

    $this->assertEquals($enum->value, $filterValues[0]);
});

test('enum name converted to value', function () {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class, fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($enum->name));

    $this->assertEquals($enum->value, $filterValues[0]);
});
