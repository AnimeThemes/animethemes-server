<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('nameable', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    expect($track->getName())->toBeString();
});

test('has subtitle', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory()->for(User::factory()))
        ->createOne();

    expect($track->getSubtitle())->toBeString();
});

test('hashids', function (): void {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    expect(array_diff([$playlist->playlist_id, $track->track_id], $track->hashids()))->toBeEmpty();
    expect(array_diff($track->hashids(), [$playlist->playlist_id, $track->track_id]))->toBeEmpty();
});

test('playlist', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->createOne();

    expect($track->playlist())->toBeInstanceOf(BelongsTo::class);
    expect($track->playlist()->first())->toBeInstanceOf(Playlist::class);
});

test('previous', function (): void {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $previous = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $track->previous()->associate($previous)->save();

    expect($track->previous())->toBeInstanceOf(BelongsTo::class);
    expect($track->previous()->first())->toBeInstanceOf(PlaylistTrack::class);
});

test('next', function (): void {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $next = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $track->next()->associate($next)->save();

    expect($track->next())->toBeInstanceOf(BelongsTo::class);
    expect($track->next()->first())->toBeInstanceOf(PlaylistTrack::class);
});

test('video', function (): void {
    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for(Video::factory())
        ->createOne();

    expect($track->video())->toBeInstanceOf(BelongsTo::class);
    expect($track->video()->first())->toBeInstanceOf(Video::class);
});
