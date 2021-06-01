<?php

declare(strict_types=1);

namespace Models;

use App\Enums\ResourceSite;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ExternalResourceTest
 * @package Models
 */
class ExternalResourceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * The site attribute of a resource shall be cast to a ResourceSite enum instance.
     *
     * @return void
     */
    public function testCastsSeasonToEnum()
    {
        $resource = ExternalResource::factory()->create();

        $site = $resource->site;

        static::assertInstanceOf(ResourceSite::class, $site);
    }

    /**
     * Resources shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $resource = ExternalResource::factory()->create();

        static::assertEquals(1, $resource->audits->count());
    }

    /**
     * Resources shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $resource = ExternalResource::factory()->create();

        static::assertIsString($resource->getName());
    }

    /**
     * Resource shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeCount = $this->faker->randomDigitNotNull;

        $resource = ExternalResource::factory()
            ->has(Anime::factory()->count($animeCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $resource->anime());
        static::assertEquals($animeCount, $resource->anime()->count());
        static::assertInstanceOf(Anime::class, $resource->anime()->first());
        static::assertEquals(AnimeResource::class, $resource->anime()->getPivotClass());
    }

    /**
     * Resource shall have a many-to-many relationship with the type Artist.
     *
     * @return void
     */
    public function testArtists()
    {
        $artistCount = $this->faker->randomDigitNotNull;

        $resource = ExternalResource::factory()
            ->has(Artist::factory()->count($artistCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $resource->artists());
        static::assertEquals($artistCount, $resource->artists()->count());
        static::assertInstanceOf(Artist::class, $resource->artists()->first());
        static::assertEquals(ArtistResource::class, $resource->artists()->getPivotClass());
    }
}
