<?php

declare(strict_types=1);

use App\Events\Wiki\Synonym\SynonymCreated;
use App\Events\Wiki\Synonym\SynonymDeleted;
use App\Events\Wiki\Synonym\SynonymRestored;
use App\Events\Wiki\Synonym\SynonymUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('synonym created event dispatched', function () {
    Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    Event::assertDispatched(SynonymCreated::class);
});

test('synonym deleted event dispatched', function () {
    $synonym = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $synonym->delete();

    Event::assertDispatched(SynonymDeleted::class);
});

test('synonym restored event dispatched', function () {
    $synonym = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $synonym->restore();

    Event::assertDispatched(SynonymRestored::class);
});

test('synonym restores quietly', function () {
    $synonym = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $synonym->restore();

    Event::assertNotDispatched(SynonymUpdated::class);
});

test('synonym updated event dispatched', function () {
    $synonym = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $changes = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->makeOne();

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Event::assertDispatched(SynonymUpdated::class);
});

test('synonym updated event embed fields', function () {
    $synonym = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $changes = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->makeOne();

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Event::assertDispatched(SynonymUpdated::class, function (SynonymUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
