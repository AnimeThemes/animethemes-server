<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('artist', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistImage->artist());
    static::assertInstanceOf(Artist::class, $artistImage->artist()->first());
});

test('image', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $artistImage->image());
    static::assertInstanceOf(Image::class, $artistImage->image()->first());
});
