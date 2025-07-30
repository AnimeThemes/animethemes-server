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

    $this->assertInstanceOf(BelongsTo::class, $studioImage->studio());
    $this->assertInstanceOf(Studio::class, $studioImage->studio()->first());
});

test('image', function () {
    $studioImage = StudioImage::factory()
        ->for(Studio::factory())
        ->for(Image::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $studioImage->image());
    $this->assertInstanceOf(Image::class, $studioImage->image()->first());
});
