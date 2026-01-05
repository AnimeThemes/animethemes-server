<?php

declare(strict_types=1);

use App\GraphQL\Filter\EnumFilter;
use Illuminate\Support\Arr;
use Tests\Unit\Enums\LocalizedEnum;
use Tests\Unit\GraphQL\Criteria\Filter\FakeCriteria;

test('enum converted to value', function () {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class);

    $criteria = new FakeCriteria($filter, $enum);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEquals($enum->value, $filterValues[0]);
});

test('enum name converted to value', function () {
    $enum = Arr::random(LocalizedEnum::cases());

    $filter = new EnumFilter(fake()->word(), LocalizedEnum::class);

    $criteria = new FakeCriteria($filter, $enum->name);

    $filterValues = $filter->getFilterValues($criteria->getFilterValues());

    $this->assertEquals($enum->value, $filterValues[0]);
});
