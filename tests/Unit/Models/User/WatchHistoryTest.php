<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $history = WatchHistory::factory()->createOne();

    $this->assertIsString($history->getName());
});

test('has subtitle', function () {
    $history = WatchHistory::factory()->createOne();

    $this->assertIsString($history->getSubtitle());
});

test('entry', function () {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->animethemeentry());
    $this->assertInstanceOf(WatchHistory::class, $history->animethemeentry()->first());
});

test('user', function () {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->user());
    $this->assertInstanceOf(User::class, $history->user()->first());
});

test('video', function () {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->video());
    $this->assertInstanceOf(Video::class, $history->video()->first());
});
