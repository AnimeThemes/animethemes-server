<?php

declare(strict_types=1);

use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $stage = SubmissionStage::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertIsString($stage->getName());
});

test('has subtitle', function () {
    $stage = SubmissionStage::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertIsString($stage->getSubtitle());
});

test('casts fields to array', function () {
    $anime = Anime::factory()->makeOne();

    $stage = SubmissionStage::factory()
        ->for(Submission::factory())
        ->createOne([SubmissionStage::ATTRIBUTE_FIELDS => $anime->attributesToArray()]);

    $this->assertIsArray($stage->fields);
});

test('submission', function () {
    $stage = SubmissionStage::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $stage->submission());
    $this->assertInstanceOf(Submission::class, $stage->submission()->first());
});
