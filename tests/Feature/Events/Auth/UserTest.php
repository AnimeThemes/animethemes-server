<?php

declare(strict_types=1);

use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('user created event dispatched', function () {
    User::factory()->createOne();

    Event::assertDispatched(UserCreated::class);
});

test('user deleted event dispatched', function () {
    $user = User::factory()->createOne();

    $user->delete();

    Event::assertDispatched(UserDeleted::class);
});

test('user restored event dispatched', function () {
    $user = User::factory()->createOne();

    $user->restore();

    Event::assertDispatched(UserRestored::class);
});

test('user restores quietly', function () {
    $user = User::factory()->createOne();

    $user->restore();

    Event::assertNotDispatched(UserUpdated::class);
});

test('user updated event dispatched', function () {
    $user = User::factory()->createOne();
    $changes = User::factory()->makeOne();

    $user->fill($changes->getAttributes());
    $user->save();

    Event::assertDispatched(UserUpdated::class);
});

test('user updated event embed fields', function () {
    $user = User::factory()->createOne();
    $changes = User::factory()->makeOne();

    $user->fill($changes->getAttributes());
    $user->save();

    Event::assertDispatched(UserUpdated::class, function (UserUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
