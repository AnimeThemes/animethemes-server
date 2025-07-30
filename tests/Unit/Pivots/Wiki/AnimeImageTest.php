<?php

declare(strict_types=1);

use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('anime', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeImage->anime());
    static::assertInstanceOf(Anime::class, $animeImage->anime()->first());
});

test('image', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $animeImage->image());
    static::assertInstanceOf(Image::class, $animeImage->image()->first());
});
