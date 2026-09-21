<?php

declare(strict_types=1);

use App\Actions\Storage\Base\MoveResults;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('default', function (): void {
    $video = Video::factory()->createOne();

    $moveResults = new MoveResults($video, fake()->word(), fake()->word());

    $result = $moveResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('failed', function (): void {
    $video = Video::factory()->createOne();

    $moves = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $moves[fake()->word()] = true;
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $moves[fake()->word()] = false;
    }

    $moveResults = new MoveResults($video, fake()->word(), fake()->word(), $moves);

    $result = $moveResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
    $video = Video::factory()->createOne();

    $moves = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $moves[fake()->word()] = true;
    }

    $moveResults = new MoveResults($video, fake()->word(), fake()->word(), $moves);

    $result = $moveResults->toActionResult();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});
