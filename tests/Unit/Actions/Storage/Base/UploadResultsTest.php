<?php

declare(strict_types=1);

use App\Actions\Storage\Base\UploadResults;
use App\Enums\Actions\ActionStatus;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('default', function (): void {
    $uploadResults = new UploadResults();

    $result = $uploadResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('failed', function (): void {
    $uploads = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = fake()->filePath();
    }

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = false;
    }

    $uploadResults = new UploadResults($uploads);

    $result = $uploadResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function (): void {
    $uploads = [];

    foreach (range(0, fake()->randomDigitNotNull()) as $ignored) {
        $uploads[fake()->word()] = fake()->filePath();
    }

    $uploadResults = new UploadResults($uploads);

    $result = $uploadResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});
