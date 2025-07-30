<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('nameable', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    static::assertIsString($track->getName());
});

test('has subtitle', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->createOne();

    static::assertIsString($track->getSubtitle());
});

test('hashids', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    static::assertEmpty(array_diff([$playlist->playlist_id, $track->track_id], $track->hashids()));
    static::assertEmpty(array_diff($track->hashids(), [$playlist->playlist_id, $track->track_id]));
});

test('playlist', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $track->playlist());
    static::assertInstanceOf(Playlist::class, $track->playlist()->first());
});

test('previous', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $previous = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $track->previous()->associate($previous)->save();

    static::assertInstanceOf(BelongsTo::class, $track->previous());
    static::assertInstanceOf(PlaylistTrack::class, $track->previous()->first());
});

test('next', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $next = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $track->next()->associate($next)->save();

    static::assertInstanceOf(BelongsTo::class, $track->next());
    static::assertInstanceOf(PlaylistTrack::class, $track->next()->first());
});

test('video', function () {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for(Video::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $track->video());
    static::assertInstanceOf(Video::class, $track->video()->first());
});
