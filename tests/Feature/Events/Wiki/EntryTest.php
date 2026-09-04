<?php

declare(strict_types=1);

use App\Events\Wiki\Entry\EntryCreated;
use App\Events\Wiki\Entry\EntryDeleted;
use App\Events\Wiki\Entry\EntryRestored;
use App\Events\Wiki\Entry\EntryUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('entry created event dispatched', function (): void {
    Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    Event::assertDispatched(EntryCreated::class);
});

test('entry deleted event dispatched', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->delete();

    Event::assertDispatched(EntryDeleted::class);
});

test('entry restored event dispatched', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->restore();

    Event::assertDispatched(EntryRestored::class);
});

test('entry restores quietly', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $entry->restore();

    Event::assertNotDispatched(EntryUpdated::class);
});

test('entry updated event dispatched', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $changes = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->makeOne();

    $entry->fill($changes->getAttributes());
    $entry->save();

    Event::assertDispatched(EntryUpdated::class);
});

test('entry updated event embed fields', function (): void {
    $entry = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->createOne();

    $changes = Entry::factory()
        ->for(Theme::factory()->for(Anime::factory()))
        ->makeOne();

    $entry->fill($changes->getAttributes());
    $entry->save();

    Event::assertDispatched(EntryUpdated::class, function (EntryUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
