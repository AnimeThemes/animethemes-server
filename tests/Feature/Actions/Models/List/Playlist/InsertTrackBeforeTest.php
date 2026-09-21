<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('first track', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne();

    $first = $playlist->first;

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackBeforeAction();

    $action->insertBefore($playlist, $track, $first);

    expect($playlist->first()->is($track))->toBeTrue();

    expect($first->previous()->is($track))->toBeTrue();

    expect($track->next()->is($first))->toBeTrue();
    expect($track->previous()->doesntExist())->toBeTrue();
});

test('last track', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne();

    $last = $playlist->last;

    $previous = $last->previous;

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackBeforeAction();

    $action->insertBefore($playlist, $track, $last);

    expect($playlist->last()->is($last))->toBeTrue();

    expect($last->previous()->is($track))->toBeTrue();

    expect($track->previous()->is($previous))->toBeTrue();
    expect($track->next()->is($last))->toBeTrue();
});
