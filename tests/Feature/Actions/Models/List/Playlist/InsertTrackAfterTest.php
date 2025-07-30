<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('last track', function () {
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

    static::assertTrue($playlist->last()->is($track));

    static::assertTrue($last->next()->is($track));

    static::assertTrue($track->previous()->is($last));
    static::assertTrue($track->next()->doesntExist());
});

test('first track', function () {
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

    static::assertTrue($playlist->first()->is($first));

    static::assertTrue($first->next()->is($track));

    static::assertTrue($track->previous()->is($first));
    static::assertTrue($track->next()->is($next));
});
