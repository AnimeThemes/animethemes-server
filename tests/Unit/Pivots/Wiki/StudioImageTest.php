<?php

declare(strict_types=1);

use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('studio', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $studioImage->studio());
    static::assertInstanceOf(Studio::class, $studioImage->studio()->first());
});

test('image', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    static::assertInstanceOf(BelongsTo::class, $studioImage->image());
    static::assertInstanceOf(Image::class, $studioImage->image()->first());
});
