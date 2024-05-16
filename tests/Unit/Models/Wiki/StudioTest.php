<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testSearchableAs(): void
    {
        $studio = Studio::factory()->createOne();

        static::assertIsString($studio->searchableAs());
    }

    /**
     * Studio shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray(): void
    {
        $studio = Studio::factory()->createOne();

        static::assertIsArray($studio->toSearchableArray());
    }

    /**
     * Studio shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $studio = Studio::factory()->createOne();

        static::assertIsString($studio->getName());
    }

    /**
     * Studio shall be subnameable.
     *
     * @return void
     */
    public function testSubNameable(): void
    {
        $studio = Studio::factory()->createOne();

        static::assertIsString($studio->getSubName());
    }

    /**
     * Studio shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime(): void
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
    public function testExternalResources(): void
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

    /**
     * Studio shall have a many-to-many relationship with the type Image.
     *
     * @return void
     */
    public function testImages(): void
    {
        $imageCount = $this->faker->randomDigitNotNull();

        $studio = Studio::factory()
            ->has(Image::factory()->count($imageCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $studio->images());
        static::assertEquals($imageCount, $studio->images()->count());
        static::assertInstanceOf(Image::class, $studio->images()->first());
        static::assertEquals(StudioImage::class, $studio->images()->getPivotClass());
    }
}
