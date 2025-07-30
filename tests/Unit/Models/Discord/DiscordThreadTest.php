<?php

declare(strict_types=1);

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('nameable', function () {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($thread->getName());
});

test('has subtitle', function () {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertIsString($thread->getSubtitle());
});

test('anime', function () {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $thread->anime());
    static::assertInstanceOf(Anime::class, $thread->anime()->first());
});
