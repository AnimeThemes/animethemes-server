<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Theme;
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
 * Class AnimeTest.
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
        $anime = Anime::factory()->createOne();

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
        $anime = Anime::factory()->createOne();

        static::assertIsString($anime->searchableAs());
    }

    /**
     * Anime shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $anime = Anime::factory()->createOne();

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

        $anime = Anime::factory()->createOne();

        static::assertEquals(1, $anime->audits()->count());
    }

    /**
     * Anime shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $anime = Anime::factory()->createOne();

        static::assertIsString($anime->getName());
    }

    /**
     * Anime shall have a one-to-many relationship with the type Synonym.
     *
     * @return void
     */
    public function testSynonyms()
    {
        $synonymCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Synonym::factory()->count($synonymCount))
            ->createOne();

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
        $seriesCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Series::factory()->count($seriesCount))
            ->createOne();

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
        $themeCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Theme::factory()->count($themeCount))
            ->createOne();

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
        $resourceCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(ExternalResource::factory()->count($resourceCount), 'resources')
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $anime->resources());
        static::assertEquals($resourceCount, $anime->resources()->count());
        static::assertInstanceOf(ExternalResource::class, $anime->resources()->first());
        static::assertEquals(AnimeResource::class, $anime->resources()->getPivotClass());
    }

    /**
     * Anime shall have a many-to-many relationship with the type Image.
     *
     * @return void
     */
    public function testImages()
    {
        $imageCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Image::factory()->count($imageCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $anime->images());
        static::assertEquals($imageCount, $anime->images()->count());
        static::assertInstanceOf(Image::class, $anime->images()->first());
        static::assertEquals(AnimeImage::class, $anime->images()->getPivotClass());
    }
}
