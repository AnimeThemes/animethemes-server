<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\SongResource;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExternalResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The site attribute of a resource shall be cast to a ResourceSite enum instance.
     */
    public function testCastsSeasonToEnum(): void
    {
        $resource = ExternalResource::factory()->createOne();

        $site = $resource->site;

        static::assertInstanceOf(ResourceSite::class, $site);
    }

    /**
     * Resources shall be nameable.
     */
    public function testNameable(): void
    {
        $resource = ExternalResource::factory()->createOne();

        static::assertIsString($resource->getName());
    }

    /**
     * Resources shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $resource = ExternalResource::factory()->createOne();

        static::assertIsString($resource->getSubtitle());
    }

    /**
     * Resource shall have a many-to-many relationship with the type Anime.
     */
    public function testAnime(): void
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
     */
    public function testArtists(): void
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
     * Resource shall have a many-to-many relationship with the type Song.
     */
    public function testSong(): void
    {
        $songCount = $this->faker->randomDigitNotNull();

        $resource = ExternalResource::factory()
            ->has(Song::factory()->count($songCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $resource->songs());
        static::assertEquals($songCount, $resource->songs()->count());
        static::assertInstanceOf(Song::class, $resource->songs()->first());
        static::assertEquals(SongResource::class, $resource->songs()->getPivotClass());
    }

    /**
     * Resource shall have a many-to-many relationship with the type Studio.
     */
    public function testStudio(): void
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
