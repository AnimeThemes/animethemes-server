<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioCreated;
use App\Events\Pivot\Wiki\AnimeStudio\AnimeStudioDeleted;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Event;

test('anime studio created event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $anime->studios()->attach($studio);

    Event::assertDispatched(AnimeStudioCreated::class);
});

test('anime studio deleted event dispatched', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $anime->studios()->attach($studio);
    $anime->studios()->detach($studio);

    Event::assertDispatched(AnimeStudioDeleted::class);
});
