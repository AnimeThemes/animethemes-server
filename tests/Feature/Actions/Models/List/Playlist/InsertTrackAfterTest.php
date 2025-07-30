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

    $this->assertTrue($playlist->last()->is($track));

    $this->assertTrue($last->next()->is($track));

    $this->assertTrue($track->previous()->is($last));
    $this->assertTrue($track->next()->doesntExist());
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

    $this->assertTrue($playlist->first()->is($first));

    $this->assertTrue($first->next()->is($track));

    $this->assertTrue($track->previous()->is($first));
    $this->assertTrue($track->next()->is($next));
});
