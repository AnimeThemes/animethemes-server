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

    static::assertTrue($playlist->first()->is($track));
    static::assertTrue($playlist->last()->is($track));
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

    static::assertTrue($playlist->first()->is($first));
    static::assertTrue($playlist->last()->is($second));

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->is($second));

    static::assertTrue($second->previous()->is($first));
    static::assertTrue($second->next()->doesntExist());
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

    static::assertTrue($playlist->first()->is($first));
    static::assertTrue($playlist->last()->is($third));

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->is($second));

    static::assertTrue($second->previous()->is($first));
    static::assertTrue($second->next()->is($third));

    static::assertTrue($third->previous()->is($second));
    static::assertTrue($third->next()->doesntExist());
});
