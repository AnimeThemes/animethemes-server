<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Sort\RandomSort;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

pest()->use(WithFaker::class);

test('format', function (): void {
    $sort = new RandomSort();

    $direction = Arr::random(Direction::cases());

    expect($sort->format($direction))->toEqual(RandomCriteria::PARAM_VALUE);
});
