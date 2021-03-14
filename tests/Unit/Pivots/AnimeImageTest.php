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
        $anime_image = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_image->anime());
        $this->assertInstanceOf(Anime::class, $anime_image->anime()->first());
    }

    /**
     * An AnimeImage shall belong to an Image.
     *
     * @return void
     */
    public function testImage()
    {
        $anime_image = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_image->image());
        $this->assertInstanceOf(Image::class, $anime_image->image()->first());
    }
}
