<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource;

/**
 * Class ExternalResourceTest.
 */
class ExternalResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The site attribute of a resource shall be cast to a ResourceSite enum instance.
     *
     * @return void
     */
    public function testCastsSeasonToEnum()
    {
        $resource = ExternalResource::factory()->createOne();

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

        $resource = ExternalResource::factory()->createOne();

        static::assertEquals(1, $resource->audits()->count());
    }

    /**
     * Resources shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $resource = ExternalResource::factory()->createOne();

        static::assertIsString($resource->getName());
    }

    /**
     * Resource shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeCount = $this->faker->randomDigitNotNull();

        $resource = ExternalResource::factory()
            ->has(Anime::factory()->count($animeCount))
            ->createOne();

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
        $artistCount = $this->faker->randomDigitNotNull();

        $resource = ExternalResource::factory()
            ->has(Artist::factory()->count($artistCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $resource->artists());
        static::assertEquals($artistCount, $resource->artists()->count());
        static::assertInstanceOf(Artist::class, $resource->artists()->first());
        static::assertEquals(ArtistResource::class, $resource->artists()->getPivotClass());
    }

    /**
     * Resource shall have a many-to-many relationshop with the type Studio.
     * 
     * @return void
     */
    public function testStudio()
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $resource = ExternalResource::factory()
            ->has(Studio::factory()->count($studioCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $resource->studios());
        static::assertEquals($studioCount, $resource->studios()->count());
        static::assertInstanceOf(Studio::class, $resource->studios()->first());
        static::assertEquals(StudioResource::class, $resource->studios()->getPivotClass());
    }
}
