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

    $this->assertInstanceOf(BelongsTo::class, $playlistImage->playlist());
    $this->assertInstanceOf(Playlist::class, $playlistImage->playlist()->first());
});

test('image', function () {
    $playlistImage = PlaylistImage::factory()
        ->for(Playlist::factory())
        ->for(Image::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $playlistImage->image());
    $this->assertInstanceOf(Image::class, $playlistImage->image()->first());
});
