<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class AnimeImageTest extends TestCase
{
    /**
     * An AnimeImage shall belong to an Anime.
     */
    public function testAnime(): void
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
     */
    public function testImage(): void
    {
        $animeImage = AnimeImage::factory()
            ->for(Anime::factory())
            ->for(Image::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeImage->image());
        static::assertInstanceOf(Image::class, $animeImage->image()->first());
    }
}
