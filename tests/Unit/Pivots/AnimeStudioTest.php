<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeStudioTest.
 */
class AnimeStudioTest extends TestCase
{
    use WithoutEvents;

    /**
     * An AnimeStudio shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
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
     *
     * @return void
     */
    public function testStudio()
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeStudio->studio());
        static::assertInstanceOf(Studio::class, $animeStudio->studio()->first());
    }
}
