<?php

declare(strict_types=1);

use App\Enums\Models\User\ApprovableStatus;
use App\Models\Auth\User;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $report = Report::factory()->createOne();

    static::assertIsString($report->getName());
});

test('has subtitle', function () {
    $report = Report::factory()->createOne();

    static::assertIsString($report->getSubtitle());
});

test('casts finished at', function () {
    $report = Report::factory()->createOne([Report::ATTRIBUTE_FINISHED_AT => now()]);

    static::assertInstanceOf(Carbon::class, $report->finished_at);
});

test('casts status to enum', function () {
    $report = Report::factory()->createOne();

    static::assertInstanceOf(ApprovableStatus::class, $report->status);
});

test('steps', function () {
    $stepsCount = fake()->randomDigitNotNull();

    $report = Report::factory()->createOne();

    ReportStep::factory()
        ->for($report)
        ->count($stepsCount)
        ->create();

    static::assertInstanceOf(HasMany::class, $report->steps());
    static::assertEquals($stepsCount, $report->steps()->count());
    static::assertInstanceOf(ReportStep::class, $report->steps()->first());
});

test('user', function () {
    $report = Report::factory()
        ->for(User::factory(), Report::RELATION_USER)
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $report->user());
    static::assertInstanceOf(User::class, $report->user()->first());
});

test('moderator', function () {
    $report = Report::factory()
        ->for(User::factory(), Report::RELATION_MODERATOR)
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $report->moderator());
    static::assertInstanceOf(User::class, $report->moderator()->first());
});
