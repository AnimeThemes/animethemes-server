<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Sort\Sort;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('default column', function (): void {
    $sort = new Sort(fake()->word());

    expect($sort->getColumn())->toEqual($sort->getKey());
});

test('format asc', function (): void {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    expect($sort->format(Direction::ASCENDING))->toEqual($sortField);
});

test('format desc', function (): void {
    $sortField = fake()->word();

    $sort = new Sort($sortField);

    expect($sort->format(Direction::DESCENDING))->toEqual("-$sortField");
});
