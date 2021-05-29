<?php

namespace Tests\Unit\Pivots;

use App\Models\Anime;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class AnimeResourceTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

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

        $this->assertInstanceOf(BelongsTo::class, $animeResource->anime());
        $this->assertInstanceOf(Anime::class, $animeResource->anime()->first());
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

        $this->assertInstanceOf(BelongsTo::class, $animeResource->resource());
        $this->assertInstanceOf(ExternalResource::class, $animeResource->resource()->first());
    }
}
