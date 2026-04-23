<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function (): void {
    $history = WatchHistory::factory()->createOne();

    $this->assertIsString($history->getName());
});

test('has subtitle', function (): void {
    $history = WatchHistory::factory()->createOne();

    $this->assertIsString($history->getSubtitle());
});

test('entry', function (): void {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->animethemeentry());
    $this->assertInstanceOf(AnimeThemeEntry::class, $history->animethemeentry()->first());
});

test('user', function (): void {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->user());
    $this->assertInstanceOf(User::class, $history->user()->first());
});

test('video', function (): void {
    $history = WatchHistory::factory()->createOne();

    $this->assertInstanceOf(BelongsTo::class, $history->video());
    $this->assertInstanceOf(Video::class, $history->video()->first());
});
