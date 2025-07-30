<?php

declare(strict_types=1);

use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Sort\RandomCriteria;
use App\Http\Api\Sort\RandomSort;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('format', function () {
    $sort = new RandomSort();

    $direction = Arr::random(Direction::cases());

    $this->assertEquals(RandomCriteria::PARAM_VALUE, $sort->format($direction));
});
