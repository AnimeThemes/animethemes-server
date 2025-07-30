<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default column', function () {
    $sort = new Sort(fake()->word());

    static::assertEquals($sort->getKey(), $sort->getColumn());
});

test('format asc', function () {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    static::assertEquals($sortField, $sort->format(Direction::ASCENDING));
});

test('format desc', function () {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    static::assertEquals("-$sortField", $sort->format(Direction::DESCENDING));
});
