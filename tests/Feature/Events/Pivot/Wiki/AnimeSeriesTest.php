<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesCreated;
use App\Events\Pivot\Wiki\AnimeSeries\AnimeSeriesDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Support\Facades\Event;

test('anime series created event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $anime->series()->attach($series);

    Event::assertDispatched(AnimeSeriesCreated::class);
});

test('anime series deleted event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $anime->series()->attach($series);
    $anime->series()->detach($series);

    Event::assertDispatched(AnimeSeriesDeleted::class);
});
