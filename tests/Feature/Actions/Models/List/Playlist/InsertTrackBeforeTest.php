<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('first track', function () {
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

    static::assertTrue($playlist->first()->is($track));

    static::assertTrue($first->previous()->is($track));

    static::assertTrue($track->next()->is($first));
    static::assertTrue($track->previous()->doesntExist());
});

test('last track', function () {
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

    static::assertTrue($playlist->last()->is($last));

    static::assertTrue($last->previous()->is($track));

    static::assertTrue($track->previous()->is($previous));
    static::assertTrue($track->next()->is($last));
});
