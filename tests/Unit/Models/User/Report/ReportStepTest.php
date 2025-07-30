<?php

declare(strict_types=1);

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\ReportActionType;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne();

    static::assertIsString($step->getName());
});

test('has subtitle', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne();

    static::assertIsString($step->getSubtitle());
});

test('casts action to enum', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne();

    static::assertInstanceOf(ReportActionType::class, $step->action);
});

test('casts fields to array', function () {
    $anime = Anime::factory()->makeOne();

    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne([ReportStep::ATTRIBUTE_FIELDS => $anime->attributesToArray()]);

    static::assertIsArray($step->fields);
});

test('casts finished at', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne([ReportStep::ATTRIBUTE_FINISHED_AT => now()]);

    static::assertInstanceOf(Carbon::class, $step->finished_at);
});

test('casts status to enum', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne();

    static::assertInstanceOf(ApprovableStatus::class, $step->status);
});

test('report', function () {
    $step = ReportStep::factory()
        ->for(Report::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $step->report());
    static::assertInstanceOf(Report::class, $step->report()->first());
});
