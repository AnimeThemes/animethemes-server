<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class ArtistImageTest extends TestCase
{
    /**
     * An ArtistImage shall belong to an Artist.
     */
    public function testArtist(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistImage->artist());
        static::assertInstanceOf(Artist::class, $artistImage->artist()->first());
    }

    /**
     * An ArtistImage shall belong to an Image.
     */
    public function testImage(): void
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistImage->image());
        static::assertInstanceOf(Image::class, $artistImage->image()->first());
    }
}
