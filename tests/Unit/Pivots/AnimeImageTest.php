<?php

namespace Tests\Unit\Pivots;

use App\Models\Anime;
use App\Models\Image;
use App\Pivots\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class AnimeImageTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * An AnimeImage shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $animeImage->anime());
        $this->assertInstanceOf(Anime::class, $animeImage->anime()->first());
    }

    /**
     * An AnimeImage shall belong to an Image.
     *
     * @return void
     */
    public function testImage()
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $animeImage->image());
        $this->assertInstanceOf(Image::class, $animeImage->image()->first());
    }
}
