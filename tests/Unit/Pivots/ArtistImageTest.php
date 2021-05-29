<?php

namespace Tests\Unit\Pivots;

use App\Models\Artist;
use App\Models\Image;
use App\Pivots\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ArtistImageTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

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

        $this->assertInstanceOf(BelongsTo::class, $artistImage->artist());
        $this->assertInstanceOf(Artist::class, $artistImage->artist()->first());
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

        $this->assertInstanceOf(BelongsTo::class, $artistImage->image());
        $this->assertInstanceOf(Image::class, $artistImage->image()->first());
    }
}
