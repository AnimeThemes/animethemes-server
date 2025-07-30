<?php

declare(strict_types=1);

use App\Actions\Storage\Base\PruneResults;
use App\Enums\Actions\ActionStatus;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $pruneResults = new PruneResults(fake()->word());

    $result = $pruneResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('failed', function () {
    $prunings = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = true;
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = false;
    }

    $pruneResults = new PruneResults(fake()->word(), $prunings);

    $result = $pruneResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    $prunings = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = true;
    }

    $pruneResults = new PruneResults(fake()->word(), $prunings);

    $result = $pruneResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});
