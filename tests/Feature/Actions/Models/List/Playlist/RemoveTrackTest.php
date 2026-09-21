<?php

declare(strict_types=1);

use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('remove sole', function (): void {
    $playlist = Playlist::factory()
        ->tracks(1)
        ->createOne();

    $first = $playlist->first;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $first);

    expect($playlist->first()->doesntExist())->toBeTrue();
    expect($playlist->last()->doesntExist())->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->doesntExist())->toBeTrue();
});

test('remove first', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $first);

    expect($playlist->first()->is($second))->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->doesntExist())->toBeTrue();

    expect($second->previous()->doesntExist())->toBeTrue();
});

test('remove last', function (): void {
    $playlist = Playlist::factory()
        ->tracks(fake()->numberBetween(3, 9))
        ->createOne();

    $last = $playlist->last;
    $previous = $last->previous;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $last);

    expect($playlist->last()->is($previous))->toBeTrue();

    expect($last->previous()->doesntExist())->toBeTrue();
    expect($last->next()->doesntExist())->toBeTrue();

    expect($previous->next()->doesntExist())->toBeTrue();
});

test('remove second', function (): void {
    $playlist = Playlist::factory()
        ->tracks(3)
        ->createOne();

    $first = $playlist->first;
    $second = $first->next;
    $third = $playlist->last;

    $action = new RemoveTrackAction();

    $action->remove($playlist, $second);

    expect($playlist->first()->is($first))->toBeTrue();
    expect($playlist->last()->is($third))->toBeTrue();

    expect($first->previous()->doesntExist())->toBeTrue();
    expect($first->next()->is($third))->toBeTrue();

    expect($second->previous()->doesntExist())->toBeTrue();
    expect($second->next()->doesntExist())->toBeTrue();

    expect($third->previous()->is($first))->toBeTrue();
    expect($third->next()->doesntExist())->toBeTrue();
});
