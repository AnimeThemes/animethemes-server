<?php

declare(strict_types=1);

use App\Events\Wiki\Theme\ThemeCreated;
use App\Events\Wiki\Theme\ThemeDeleted;
use App\Events\Wiki\Theme\ThemeRestored;
use App\Events\Wiki\Theme\ThemeUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('theme created event dispatched', function (): void {
    Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    Event::assertDispatched(ThemeCreated::class);
});

test('theme deleted event dispatched', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->delete();

    Event::assertDispatched(ThemeDeleted::class);
});

test('theme restored event dispatched', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->restore();

    Event::assertDispatched(ThemeRestored::class);
});

test('theme restores quietly', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->restore();

    Event::assertNotDispatched(ThemeUpdated::class);
});

test('theme updated event dispatched', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = Theme::factory()
        ->for(Anime::factory())
        ->makeOne();

    $theme->fill($changes->getAttributes());
    $theme->save();

    Event::assertDispatched(ThemeUpdated::class);
});

test('theme updated event embed fields', function (): void {
    $theme = Theme::factory()
        ->for(Anime::factory())
        ->createOne();

    $changes = Theme::factory()
        ->for(Anime::factory())
        ->makeOne();

    $theme->fill($changes->getAttributes());
    $theme->save();

    Event::assertDispatched(ThemeUpdated::class, function (ThemeUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
