<?php

declare(strict_types=1);

use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Models\Wiki\Audio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('audio created event dispatched', function () {
    Audio::factory()->createOne();

    Event::assertDispatched(AudioCreated::class);
});

test('audio deleted event dispatched', function () {
    $audio = Audio::factory()->createOne();

    $audio->delete();

    Event::assertDispatched(AudioDeleted::class);
});

test('audio restored event dispatched', function () {
    $audio = Audio::factory()->createOne();

    $audio->restore();

    Event::assertDispatched(AudioRestored::class);
});

test('audio restores quietly', function () {
    $audio = Audio::factory()->createOne();

    $audio->restore();

    Event::assertNotDispatched(AudioUpdated::class);
});

test('audio updated event dispatched', function () {
    $audio = Audio::factory()->createOne();
    $changes = Audio::factory()->makeOne();

    $audio->fill($changes->getAttributes());
    $audio->save();

    Event::assertDispatched(AudioUpdated::class);
});

test('audio updated event embed fields', function () {
    $audio = Audio::factory()->createOne();
    $changes = Audio::factory()->makeOne();

    $audio->fill($changes->getAttributes());
    $audio->save();

    Event::assertDispatched(AudioUpdated::class, function (AudioUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
