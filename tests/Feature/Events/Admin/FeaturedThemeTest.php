<?php

declare(strict_types=1);

use App\Events\Admin\FeaturedTheme\FeaturedThemeCreated;
use App\Events\Admin\FeaturedTheme\FeaturedThemeDeleted;
use App\Events\Admin\FeaturedTheme\FeaturedThemeUpdated;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('featured theme created event dispatched', function () {
    FeaturedTheme::factory()->create();

    Event::assertDispatched(FeaturedThemeCreated::class);
});

test('featured theme deleted event dispatched', function () {
    $featuredTheme = FeaturedTheme::factory()->create();

    $featuredTheme->delete();

    Event::assertDispatched(FeaturedThemeDeleted::class);
});

test('featured theme updated event dispatched', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();
    $changes = FeaturedTheme::factory()->makeOne();

    $featuredTheme->fill($changes->getAttributes());
    $featuredTheme->save();

    Event::assertDispatched(FeaturedThemeUpdated::class);
});

test('featured theme updated event embed fields', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();
    $changes = FeaturedTheme::factory()->makeOne();

    $featuredTheme->fill($changes->getAttributes());
    $featuredTheme->save();

    Event::assertDispatched(FeaturedThemeUpdated::class, function (FeaturedThemeUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
