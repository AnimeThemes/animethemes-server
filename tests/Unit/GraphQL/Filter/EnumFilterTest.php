<?php

declare(strict_types=1);

use App\GraphQL\Filter\EnumFilter;
use Illuminate\Support\Arr;
use Tests\Unit\Enums\LocalizedEnum;

test('enum converted to value', function (): void {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class, fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($enum));

    expect($filterValues[0])->toEqual($enum->value);
});

test('enum name converted to value', function (): void {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class, fake()->word());

    $filterValues = $filter->getFilterValues(Arr::wrap($enum->name));

    expect($filterValues[0])->toEqual($enum->value);
});
