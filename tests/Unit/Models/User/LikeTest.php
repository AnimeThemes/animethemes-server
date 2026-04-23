<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('nameable', function (): void {
    $like = Like::factory()
        ->forPlaylist()
        ->createOne();

    $this->assertIsString($like->getName());
});

test('has subtitle', function (): void {
    $like = Like::factory()
        ->forPlaylist()
        ->createOne();

    $this->assertIsString($like->getSubtitle());
});

test('playlist', function (): void {
    $like = Like::factory()
        ->forPlaylist()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $like->likeable());
    $this->assertInstanceOf(Playlist::class, $like->likeable()->first());
});

test('entry', function (): void {
    $like = Like::factory()
        ->forEntry()
        ->createOne();

    $this->assertInstanceOf(MorphTo::class, $like->likeable());
    $this->assertInstanceOf(AnimeThemeEntry::class, $like->likeable()->first());
});

test('user', function (): void {
    $like = Like::factory()
        ->forPlaylist()
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $like->user());
    $this->assertInstanceOf(User::class, $like->user()->first());
});
