<?php

declare(strict_types=1);

namespace Models;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\Synonym;
use App\Models\Theme;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class AnimeTest
 * @package Models
 */
class AnimeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * The season attribute of an anime shall be cast to an AnimeSeason enum instance.
     *
     * @return void
     */
    public function testCastsSeasonToEnum()
    {
        $anime = Anime::factory()->create();

        $season = $anime->season;

        static::assertInstanceOf(AnimeSeason::class, $season);
    }

    /**
     * Anime shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $anime = Anime::factory()->create();

        static::assertIsString($anime->searchableAs());
    }

    /**
     * Anime shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $anime = Anime::factory()->create();

        static::assertIsArray($anime->toSearchableArray());
    }

    /**
     * Anime shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $anime = Anime::factory()->create();

        static::assertEquals(1, $anime->audits->count());
    }

    /**
     * Anime shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $anime = Anime::factory()->create();

        static::assertIsString($anime->getName());
    }

    /**
     * Anime shall have a one-to-many relationship with the type Synonym.
     *
     * @return void
     */
    public function testSynonyms()
    {
        $synonymCount = $this->faker->randomDigitNotNull;

        $anime = Anime::factory()
            ->has(Synonym::factory()->count($synonymCount))
            ->create();

        static::assertInstanceOf(HasMany::class, $anime->synonyms());
        static::assertEquals($synonymCount, $anime->synonyms()->count());
        static::assertInstanceOf(Synonym::class, $anime->synonyms()->first());
    }

    /**
     * Anime shall have a many-to-many relationship with the type Series.
     *
     * @return void
     */
    public function testSeries()
    {
        $seriesCount = $this->faker->randomDigitNotNull;

        $anime = Anime::factory()
            ->has(Series::factory()->count($seriesCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $anime->series());
        static::assertEquals($seriesCount, $anime->series()->count());
        static::assertInstanceOf(Series::class, $anime->series()->first());
        static::assertEquals(AnimeSeries::class, $anime->series()->getPivotClass());
    }

    /**
     * Anime shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function testThemes()
    {
        $themeCount = $this->faker->randomDigitNotNull;

        $anime = Anime::factory()
            ->has(Theme::factory()->count($themeCount))
            ->create();

        static::assertInstanceOf(HasMany::class, $anime->themes());
        static::assertEquals($themeCount, $anime->themes()->count());
        static::assertInstanceOf(Theme::class, $anime->themes()->first());
    }

    /**
     * Anime shall have a many-to-many relationship with the type ExternalResource.
     *
     * @return void
     */
    public function testExternalResources()
    {
        $resourceCount = $this->faker->randomDigitNotNull;

        $anime = Anime::factory()
            ->has(ExternalResource::factory()->count($resourceCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $anime->externalResources());
        static::assertEquals($resourceCount, $anime->externalResources()->count());
        static::assertInstanceOf(ExternalResource::class, $anime->externalResources()->first());
        static::assertEquals(AnimeResource::class, $anime->externalResources()->getPivotClass());
    }

    /**
     * Anime shall have a many-to-many relationship with the type Image.
     *
     * @return void
     */
    public function testImages()
    {
        $imageCount = $this->faker->randomDigitNotNull;

        $anime = Anime::factory()
            ->has(Image::factory()->count($imageCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $anime->images());
        static::assertEquals($imageCount, $anime->images()->count());
        static::assertInstanceOf(Image::class, $anime->images()->first());
        static::assertEquals(AnimeImage::class, $anime->images()->getPivotClass());
    }
}
