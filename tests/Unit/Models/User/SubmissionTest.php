<?php

declare(strict_types=1);

use App\Enums\Models\User\ApprovableStatus;
use App\Models\Auth\User;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    $this->assertInstanceOf(ApprovableStatus::class, $submission->status);
});

test('steps', function () {
    $stepsCount = fake()->randomDigitNotNull();

    $submission = Submission::factory()->createOne();

    SubmissionStep::factory()
        ->for($submission)
        ->count($stepsCount)
        ->create();

    $this->assertInstanceOf(HasMany::class, $submission->steps());
    $this->assertEquals($stepsCount, $submission->steps()->count());
    $this->assertInstanceOf(SubmissionStep::class, $submission->steps()->first());
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
