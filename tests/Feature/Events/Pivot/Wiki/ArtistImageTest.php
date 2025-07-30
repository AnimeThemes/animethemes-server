<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\ArtistImage\ArtistImageCreated;
use App\Events\Pivot\Wiki\ArtistImage\ArtistImageDeleted;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Event;

test('artist image created event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $artist->images()->attach($image);

    Event::assertDispatched(ArtistImageCreated::class);
});

test('artist image deleted event dispatched', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $artist->images()->attach($image);
    $artist->images()->detach($image);

    Event::assertDispatched(ArtistImageDeleted::class);
});
