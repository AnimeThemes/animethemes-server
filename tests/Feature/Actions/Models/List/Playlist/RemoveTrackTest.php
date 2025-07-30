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

    $this->assertTrue($playlist->first()->doesntExist());
    $this->assertTrue($playlist->last()->doesntExist());

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->doesntExist());
});

test('remove first', function () {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $first);

    $this->assertTrue($playlist->first()->is($second));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->doesntExist());

    $this->assertTrue($second->previous()->doesntExist());
});

test('remove last', function () {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $last = $playlist->last;
    $previous = $last->previous;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $last);

    $this->assertTrue($playlist->last()->is($previous));

    $this->assertTrue($last->previous()->doesntExist());
    $this->assertTrue($last->next()->doesntExist());

    $this->assertTrue($previous->next()->doesntExist());
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

    $this->assertTrue($playlist->first()->is($first));
    $this->assertTrue($playlist->last()->is($third));

    $this->assertTrue($first->previous()->doesntExist());
    $this->assertTrue($first->next()->is($third));

    $this->assertTrue($second->previous()->doesntExist());
    $this->assertTrue($second->next()->doesntExist());

    $this->assertTrue($third->previous()->is($first));
    $this->assertTrue($third->next()->doesntExist());
});
