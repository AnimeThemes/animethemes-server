<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\AnimeImage\AnimeImageCreated;
use App\Events\Pivot\Wiki\AnimeImage\AnimeImageDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;

test('anime image created event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $anime->images()->attach($image);

    Event::assertDispatched(AnimeImageCreated::class);
});

test('anime image deleted event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $anime->images()->attach($image);
    $anime->images()->detach($image);

    Event::assertDispatched(AnimeImageDeleted::class);
});
