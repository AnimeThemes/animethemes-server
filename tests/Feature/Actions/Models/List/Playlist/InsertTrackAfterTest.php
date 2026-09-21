<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('last track', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne();

    $last = $playlist->last;

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAfterAction();

    $action->insertAfter($playlist, $track, $last);

    expect($playlist->last()->is($track))->toBeTrue();

    expect($last->next()->is($track))->toBeTrue();

    expect($track->previous()->is($last))->toBeTrue();
    expect($track->next()->doesntExist())->toBeTrue();
});

test('first track', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(2, 9))
        ->createOne();

    $first = $playlist->first;

    $next = $first->next;

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAfterAction();

    $action->insertAfter($playlist, $track, $first);

    expect($playlist->first()->is($first))->toBeTrue();

    expect($first->next()->is($track))->toBeTrue();

    expect($track->previous()->is($first))->toBeTrue();
    expect($track->next()->is($next))->toBeTrue();
});
