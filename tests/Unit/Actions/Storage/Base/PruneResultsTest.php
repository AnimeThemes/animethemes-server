<?php

declare(strict_types=1);

use App\Actions\Storage\Base\PruneResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('default', function (): void {
    $pruneResults = new PruneResults(fake()->word());

    $result = $pruneResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('failed', function (): void {
    $prunings = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = true;
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = false;
    }

    $pruneResults = new PruneResults(fake()->word(), $prunings);

    $result = $pruneResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
    $prunings = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $prunings[fake()->word()] = true;
    }

    $pruneResults = new PruneResults(fake()->word(), $prunings);

    $result = $pruneResults->toActionResult();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});
