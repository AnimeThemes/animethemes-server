<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class AnimeStudioTest extends TestCase
{
    /**
     * An AnimeStudio shall belong to an Anime.
     */
    public function testAnime(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeStudio->anime());
        static::assertInstanceOf(Anime::class, $animeStudio->anime()->first());
    }

    /**
     * An AnimeStudio shall belong to a Studio.
     */
    public function testStudio(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeStudio->studio());
        static::assertInstanceOf(Studio::class, $animeStudio->studio()->first());
    }
}
