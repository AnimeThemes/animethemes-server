<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class AnimeResourceTest extends TestCase
{
    /**
     * An AnimeResource shall belong to an Anime.
     */
    public function testAnime(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeResource->anime());
        static::assertInstanceOf(Anime::class, $animeResource->anime()->first());
    }

    /**
     * An AnimeResource shall belong to an ExternalResource.
     */
    public function testResource(): void
    {
        $animeResource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeResource->resource());
        static::assertInstanceOf(ExternalResource::class, $animeResource->resource()->first());
    }
}
