<?php

declare(strict_types=1);

use App\Enums\Models\User\SubmissionStatus;
use App\Models\Auth\User;
use App\Models\User\Submission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $submission = Submission::factory()->createOne();

    $this->assertIsString($submission->getName());
});

test('has subtitle', function () {
    $submission = Submission::factory()->createOne();

    $this->assertIsString($submission->getSubtitle());
});

test('casts finished at', function () {
    $submission = Submission::factory()->createOne([Submission::ATTRIBUTE_FINISHED_AT => now()]);

    $this->assertInstanceOf(Carbon::class, $submission->finished_at);
});

test('casts status to enum', function () {
    $submission = Submission::factory()->createOne();

    $this->assertInstanceOf(SubmissionStatus::class, $submission->status);
});

test('user', function () {
    $submission = Submission::factory()
        ->for(User::factory(), Submission::RELATION_USER)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $submission->user());
    $this->assertInstanceOf(User::class, $submission->user()->first());
});

test('moderator', function () {
    $submission = Submission::factory()
        ->for(User::factory(), Submission::RELATION_MODERATOR)
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $submission->moderator());
    $this->assertInstanceOf(User::class, $submission->moderator()->first());
});
