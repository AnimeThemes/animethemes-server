<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;

test('first track', function (): void {
    $playlist = Playlist::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for($playlist)
        ->for(Video::factory())
        ->createOne();

    $action = new InsertTrackAction();

    $action->insert($playlist, $track);

    expect($playlist->first()->is($track))->toBeTrue();
    expect($playlist->last()->is($track))->toBeTrue();
});

test('second track', function (): void {
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

    expect($playlist->first()->is($first))->toBeTrue();
    expect($playlist->last()->is($second))->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->is($second))->toBeTrue();

    expect($second->previous()->is($first))->toBeTrue();
    expect($second->next()->doesntExist())->toBeTrue();
});

test('third track', function (): void {
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

    expect($playlist->first()->is($first))->toBeTrue();
    expect($playlist->last()->is($third))->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->is($second))->toBeTrue();

    expect($second->previous()->is($first))->toBeTrue();
    expect($second->next()->is($third))->toBeTrue();

    expect($third->previous()->is($second))->toBeTrue();
    expect($third->next()->doesntExist())->toBeTrue();
});
