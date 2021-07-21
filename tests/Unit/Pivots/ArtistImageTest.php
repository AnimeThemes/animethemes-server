<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistImageTest.
 */
class ArtistImageTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

    /**
     * An ArtistImage shall belong to an Artist.
     *
     * @return void
     */
    public function testArtist()
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
     *
     * @return void
     */
    public function testImage()
    {
        $artistImage = ArtistImage::factory()
            ->for(Artist::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $artistImage->image());
        static::assertInstanceOf(Image::class, $artistImage->image()->first());
    }
}
