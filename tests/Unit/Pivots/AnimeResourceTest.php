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
        $anime_resource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_resource->anime());
        $this->assertInstanceOf(Anime::class, $anime_resource->anime()->first());
    }

    /**
     * An AnimeResource shall belong to an ExternalResource.
     *
     * @return void
     */
    public function testResource()
    {
        $anime_resource = AnimeResource::factory()
            ->for(Anime::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_resource->resource());
        $this->assertInstanceOf(ExternalResource::class, $anime_resource->resource()->first());
    }
}
