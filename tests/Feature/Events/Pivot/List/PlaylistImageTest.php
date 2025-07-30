<?php

declare(strict_types=1);

use App\Events\Pivot\List\PlaylistImage\PlaylistImageCreated;
use App\Events\Pivot\List\PlaylistImage\PlaylistImageDeleted;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;

test('playlist image created event dispatched', function () {
    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $playlist->images()->attach($image);

    Event::assertDispatched(PlaylistImageCreated::class);
});

test('playlist image deleted event dispatched', function () {
    $playlist = Playlist::factory()->createOne();
    $image = Image::factory()->createOne();

    $playlist->images()->attach($image);
    $playlist->images()->detach($image);

    Event::assertDispatched(PlaylistImageDeleted::class);
});
