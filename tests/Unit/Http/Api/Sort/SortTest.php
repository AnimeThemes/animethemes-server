<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('default column', function (): void {
    $sort = new Sort(fake()->word());

    $this->assertEquals($sort->getKey(), $sort->getColumn());
});

test('format asc', function (): void {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    $this->assertEquals($sortField, $sort->format(Direction::ASCENDING));
});

test('format desc', function (): void {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    $this->assertEquals("-$sortField", $sort->format(Direction::DESCENDING));
});
