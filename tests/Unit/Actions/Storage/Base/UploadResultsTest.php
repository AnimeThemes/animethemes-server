<?php

declare(strict_types=1);

use App\Actions\Storage\Base\UploadResults;
use App\Enums\Actions\ActionStatus;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $uploadResults = new UploadResults();

    $result = $uploadResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('failed', function () {
    $uploads = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = fake()->filePath();
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = false;
    }

    $uploadResults = new UploadResults($uploads);

    $result = $uploadResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    $uploads = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = fake()->filePath();
    }

    $uploadResults = new UploadResults($uploads);

    $result = $uploadResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});
