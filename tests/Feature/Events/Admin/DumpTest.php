<?php

declare(strict_types=1);

use App\Events\Admin\Dump\DumpCreated;
use App\Events\Admin\Dump\DumpDeleted;
use App\Events\Admin\Dump\DumpUpdated;
use App\Models\Admin\Dump;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('dump created event dispatched', function (): void {
    Dump::factory()->create();

    Event::assertDispatched(DumpCreated::class);
});

test('dump deleted event dispatched', function (): void {
    $dump = Dump::factory()->create();

    $dump->delete();

    Event::assertDispatched(DumpDeleted::class);
});

test('dump updated event dispatched', function (): void {
    $dump = Dump::factory()->createOne();
    $changes = Dump::factory()->makeOne();

    $dump->fill($changes->getAttributes());
    $dump->save();

    Event::assertDispatched(DumpUpdated::class);
});

test('dump updated event embed fields', function (): void {
    $dump = Dump::factory()->createOne();
    $changes = Dump::factory()->makeOne();

    $dump->fill($changes->getAttributes());
    $dump->save();

    Event::assertDispatched(DumpUpdated::class, function (DumpUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
