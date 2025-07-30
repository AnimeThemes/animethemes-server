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

    $this->assertTrue($playlist->first()->is($track));

    $this->assertTrue($first->previous()->is($track));

    $this->assertTrue($track->next()->is($first));
    $this->assertTrue($track->previous()->doesntExist());
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

    $this->assertTrue($playlist->last()->is($last));

    $this->assertTrue($last->previous()->is($track));

    $this->assertTrue($track->previous()->is($previous));
    $this->assertTrue($track->next()->is($last));
});
