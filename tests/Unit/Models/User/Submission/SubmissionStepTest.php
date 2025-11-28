<?php

declare(strict_types=1);

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStep;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertIsString($step->getName());
});

test('has subtitle', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertIsString($step->getSubtitle());
});

test('casts action to enum', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertInstanceOf(SubmissionActionType::class, $step->action);
});

test('casts fields to array', function () {
    $anime = Anime::factory()->makeOne();

    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne([SubmissionStep::ATTRIBUTE_FIELDS => $anime->attributesToArray()]);

    $this->assertIsArray($step->fields);
});

test('casts finished at', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne([SubmissionStep::ATTRIBUTE_FINISHED_AT => now()]);

    $this->assertInstanceOf(Carbon::class, $step->finished_at);
});

test('casts status to enum', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertInstanceOf(ApprovableStatus::class, $step->status);
});

test('submission', function () {
    $step = SubmissionStep::factory()
        ->for(Submission::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $step->submission());
    $this->assertInstanceOf(Submission::class, $step->submission()->first());
});
