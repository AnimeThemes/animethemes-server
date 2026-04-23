<?php

declare(strict_types=1);

use App\Events\Wiki\Audio\AudioCreated;
use App\Events\Wiki\Audio\AudioDeleted;
use App\Events\Wiki\Audio\AudioRestored;
use App\Events\Wiki\Audio\AudioUpdated;
use App\Models\Wiki\Audio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('audio created event dispatched', function (): void {
    Audio::factory()->createOne();

    Event::assertDispatched(AudioCreated::class);
});

test('audio deleted event dispatched', function (): void {
    $audio = Audio::factory()->createOne();

    $audio->delete();

    Event::assertDispatched(AudioDeleted::class);
});

test('audio restored event dispatched', function (): void {
    $audio = Audio::factory()->createOne();

    $audio->restore();

    Event::assertDispatched(AudioRestored::class);
});

test('audio restores quietly', function (): void {
    $audio = Audio::factory()->createOne();

    $audio->restore();

    Event::assertNotDispatched(AudioUpdated::class);
});

test('audio updated event dispatched', function (): void {
    $audio = Audio::factory()->createOne();
    $changes = Audio::factory()->makeOne();

    $audio->fill($changes->getAttributes());
    $audio->save();

    Event::assertDispatched(AudioUpdated::class);
});

test('audio updated event embed fields', function (): void {
    $audio = Audio::factory()->createOne();
    $changes = Audio::factory()->makeOne();

    $audio->fill($changes->getAttributes());
    $audio->save();

    Event::assertDispatched(AudioUpdated::class, function (AudioUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
