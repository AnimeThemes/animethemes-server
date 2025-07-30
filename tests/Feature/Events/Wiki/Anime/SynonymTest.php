<?php

declare(strict_types=1);

use App\Events\Wiki\Anime\Synonym\SynonymCreated;
use App\Events\Wiki\Anime\Synonym\SynonymDeleted;
use App\Events\Wiki\Anime\Synonym\SynonymRestored;
use App\Events\Wiki\Anime\Synonym\SynonymUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('synonym created event dispatched', function () {
    AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    Event::assertDispatched(SynonymCreated::class);
});

test('synonym deleted event dispatched', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $synonym->delete();

    Event::assertDispatched(SynonymDeleted::class);
});

test('synonym restored event dispatched', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $synonym->restore();

    Event::assertDispatched(SynonymRestored::class);
});

test('synonym restores quietly', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $synonym->restore();

    Event::assertNotDispatched(SynonymUpdated::class);
});

test('synonym updated event dispatched', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->makeOne();

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Event::assertDispatched(SynonymUpdated::class);
});

test('synonym updated event embed fields', function () {
    $synonym = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = AnimeSynonym::factory()
        ->for(Anime::factory())
        ->makeOne();

    $synonym->fill($changes->getAttributes());
    $synonym->save();

    Event::assertDispatched(SynonymUpdated::class, function (SynonymUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
