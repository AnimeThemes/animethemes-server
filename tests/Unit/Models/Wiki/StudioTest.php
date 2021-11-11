<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\AnimeStudio;
use App\Pivots\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class StudioTest.
 */
class StudioTest extends TestCase
{
    use WithFaker;

    /**
     * Studio shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $studio = Studio::factory()->createOne();

        static::assertIsString($studio->searchableAs());
    }

    /**
     * Studio shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $studio = Studio::factory()->createOne();

        static::assertIsArray($studio->toSearchableArray());
    }

    /**
     * Studio shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $studio = Studio::factory()->createOne();

        static::assertEquals(1, $studio->audits()->count());
    }

    /**
     * Studio shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $studio = Studio::factory()->createOne();

        static::assertIsString($studio->getName());
    }

    /**
     * Studio shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeCount = $this->faker->randomDigitNotNull();

        $studio = Studio::factory()
            ->has(Anime::factory()->count($animeCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $studio->anime());
        static::assertEquals($animeCount, $studio->anime()->count());
        static::assertInstanceOf(Anime::class, $studio->anime()->first());
        static::assertEquals(AnimeStudio::class, $studio->anime()->getPivotClass());
    }

    /**
     * Studio shall have a many-to-many relationship with the type ExternalResource.
     *
     * @return void
     */
    public function testExternalResources()
    {
        $resourceCount = $this->faker->randomDigitNotNull();

        $studio = Studio::factory()
            ->has(ExternalResource::factory()->count($resourceCount), 'resources')
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $studio->resources());
        static::assertEquals($resourceCount, $studio->resources()->count());
        static::assertInstanceOf(ExternalResource::class, $studio->resources()->first());
        static::assertEquals(StudioResource::class, $studio->resources()->getPivotClass());
    }
}
