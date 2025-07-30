<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Event;

test('studio image created event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $studio->images()->attach($image);

    Event::assertDispatched(StudioImageCreated::class);
});

test('studio image deleted event dispatched', function () {
    $studio = Studio::factory()->createOne();
    $image = Image::factory()->createOne();

    $studio->images()->attach($image);
    $studio->images()->detach($image);

    Event::assertDispatched(StudioImageDeleted::class);
});
