<?php

declare(strict_types=1);

use App\Actions\Storage\Base\DeleteResults;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $video = Video::factory()->createOne();

    $deleteResults = new DeleteResults($video);

    $result = $deleteResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('failed', function () {
    $video = Video::factory()->createOne();

    $deletions = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $deletions[fake()->word()] = true;
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $deletions[fake()->word()] = false;
    }

    $deleteResults = new DeleteResults($video, $deletions);

    $result = $deleteResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    $video = Video::factory()->createOne();

    $deletions = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $deletions[fake()->word()] = true;
    }

    $deleteResults = new DeleteResults($video, $deletions);

    $result = $deleteResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});
