<?php

declare(strict_types=1);

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function (): void {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($thread->getName());
});

test('has subtitle', function (): void {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertIsString($thread->getSubtitle());
});

test('anime', function (): void {
    $thread = DiscordThread::factory()
        ->for(Anime::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $thread->anime());
    $this->assertInstanceOf(Anime::class, $thread->anime()->first());
});
