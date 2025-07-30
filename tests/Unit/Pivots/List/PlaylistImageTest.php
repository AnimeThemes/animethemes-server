<?php

declare(strict_types=1);

use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('playlist', function () {
    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $playlistImage->playlist());
    static::assertInstanceOf(Playlist::class, $playlistImage->playlist()->first());
});

test('image', function () {
    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $playlistImage->image());
    static::assertInstanceOf(Image::class, $playlistImage->image()->first());
});
