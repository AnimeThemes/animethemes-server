<?php

declare(strict_types=1);

use App\Actions\Storage\Base\MoveResults;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $video = Video::factory()->createOne();

    $moveResults = new MoveResults($video, fake()->word(), fake()->word());

    $result = $moveResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('failed', function () {
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

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    $video = Video::factory()->createOne();

    $moves = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $moves[fake()->word()] = true;
    }

    $moveResults = new MoveResults($video, fake()->word(), fake()->word(), $moves);

    $result = $moveResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});
