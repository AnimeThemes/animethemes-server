<?php

declare(strict_types=1);

use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('theme created event dispatched', function () {
    AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    Event::assertDispatched(ThemeCreated::class);
});

test('theme deleted event dispatched', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->delete();

    Event::assertDispatched(ThemeDeleted::class);
});

test('theme restored event dispatched', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->restore();

    Event::assertDispatched(ThemeRestored::class);
});

test('theme restores quietly', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->restore();

    Event::assertNotDispatched(ThemeUpdated::class);
});

test('theme updated event dispatched', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = AnimeTheme::factory()
        ->for(Anime::factory())
        ->makeOne();

    $theme->fill($changes->getAttributes());
    $theme->save();

    Event::assertDispatched(ThemeUpdated::class);
});

test('theme updated event embed fields', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = AnimeTheme::factory()
        ->for(Anime::factory())
        ->makeOne();

    $theme->fill($changes->getAttributes());
    $theme->save();

    Event::assertDispatched(ThemeUpdated::class, function (ThemeUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
