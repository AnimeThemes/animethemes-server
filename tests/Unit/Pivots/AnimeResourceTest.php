<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeResourceTest.
 */
class AnimeResourceTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

    /**
     * An AnimeResource shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        static::assertInstanceOf(BelongsTo::class, $animeResource->anime());
        static::assertInstanceOf(Anime::class, $animeResource->anime()->first());
    }

    /**
     * An AnimeResource shall belong to an ExternalResource.
     *
     * @return void
     */
    public function testResource()
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        static::assertInstanceOf(BelongsTo::class, $animeResource->resource());
        static::assertInstanceOf(ExternalResource::class, $animeResource->resource()->first());
    }
}