<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class StudioImageTest extends TestCase
{
    /**
     * An StudioImage shall belong to a Studio.
     */
    public function testStudio(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $studioImage->studio());
        static::assertInstanceOf(Studio::class, $studioImage->studio()->first());
    }

    /**
     * An StudioImage shall belong to an Image.
     */
    public function testImage(): void
    {
        $studioImage = StudioImage::factory()
            ->for(Studio::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $studioImage->image());
        static::assertInstanceOf(Image::class, $studioImage->image()->first());
    }
}
