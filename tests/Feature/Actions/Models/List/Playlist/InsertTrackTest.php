<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;

test('first track', function () {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAction();

    $action->insert($playlist, $track);

    $this->assertTrue($playlist->first()->is($track));
    $this->assertTrue($playlist->last()->is($track));
});

test('second track', function () {
    $playlist = Playlist::factory()->createOne();

    $first = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $second = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAction();

    $action->insert($playlist, $first);
    $action->insert($playlist, $second);

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($second));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($second));

    $this->assertTrue($second->previous()->is($first));
    $this->assertTrue($second->next()->doesntExist());
});

test('third track', function () {
    $playlist = Playlist::factory()->createOne();

    $first = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $second = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $third = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAction();

    $action->insert($playlist, $first);
    $action->insert($playlist, $second);
    $action->insert($playlist, $third);

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($second));

    $this->assertTrue($second->previous()->is($first));
    $this->assertTrue($second->next()->is($third));

    $this->assertTrue($third->previous()->is($second));
    $this->assertTrue($third->next()->doesntExist());
});
