<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeImageTest.
 */
class AnimeImageTest extends TestCase
{
    use WithoutEvents;

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
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeImage->anime());
        static::assertInstanceOf(Anime::class, $animeImage->anime()->first());
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
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeImage->image());
        static::assertInstanceOf(Image::class, $animeImage->image()->first());
    }
}
