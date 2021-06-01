<?php

declare(strict_types=1);

namespace Pivots;

use App\Models\Artist;
use App\Models\Image;
use App\Pivots\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistImageTest
 * @package Pivots
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
            ->create();

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
            ->create();

        static::assertInstanceOf(BelongsTo::class, $artistImage->image());
        static::assertInstanceOf(Image::class, $artistImage->image()->first());
    }
}
