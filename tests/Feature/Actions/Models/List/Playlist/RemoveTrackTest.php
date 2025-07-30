<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('remove sole', function () {
    $playlist = Playlist::factory()
        ->tracks(1)
        ->createOne();

    $first = $playlist->first;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $first);

    static::assertTrue($playlist->first()->doesntExist());
    static::assertTrue($playlist->last()->doesntExist());

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->doesntExist());
});

test('remove first', function () {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $first);

    static::assertTrue($playlist->first()->is($second));

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->doesntExist());

    static::assertTrue($second->previous()->doesntExist());
});

test('remove last', function () {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $last = $playlist->last;
    $previous = $last->previous;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $last);

    static::assertTrue($playlist->last()->is($previous));

    static::assertTrue($last->previous()->doesntExist());
    static::assertTrue($last->next()->doesntExist());

    static::assertTrue($previous->next()->doesntExist());
});

test('remove second', function () {
    $playlist = Playlist::factory()
        ->tracks(3)
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;
    $third = $playlist->last;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $second);

    static::assertTrue($playlist->first()->is($first));
    static::assertTrue($playlist->last()->is($third));

    static::assertTrue($first->previous()->doesntExist());
    static::assertTrue($first->next()->is($third));

    static::assertTrue($second->previous()->doesntExist());
    static::assertTrue($second->next()->doesntExist());

    static::assertTrue($third->previous()->is($first));
    static::assertTrue($third->next()->doesntExist());
});
