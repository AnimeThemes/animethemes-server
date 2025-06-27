<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AnimeTest.
 */
class AnimeTest extends TestCase
{
    use WithFaker;

    /**
     * The season attribute of an anime shall be cast to an AnimeSeason enum instance.
     *
     * @return void
     */
    public function test_casts_season_to_enum(): void
    {
        $anime = Anime::factory()->createOne();

        $season = $anime->season;

        static::assertInstanceOf(AnimeSeason::class, $season);
    }

    /**
     * The media_format attribute of an anime shall be cast to an AnimeMediaFormat enum instance.
     *
     * @return void
     */
    public function test_casts_media_format_to_enum(): void
    {
        $anime = Anime::factory()->createOne();

        $media_format = $anime->media_format;

        static::assertInstanceOf(AnimeMediaFormat::class, $media_format);
    }

    /**
     * Anime shall be a searchable resource.
     *
     * @return void
     */
    public function test_searchable_as(): void
    {
        $anime = Anime::factory()->createOne();

        static::assertIsString($anime->searchableAs());
    }

    /**
     * Anime shall be a searchable resource.
     *
     * @return void
     */
    public function test_to_searchable_array(): void
    {
        $anime = Anime::factory()->createOne();

        static::assertIsArray($anime->toSearchableArray());
    }

    /**
     * Anime shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $anime = Anime::factory()->createOne();

        static::assertIsString($anime->getName());
    }

    /**
     * Anime shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $anime = Anime::factory()->createOne();

        static::assertIsString($anime->getSubtitle());
    }

    /**
     * Anime shall have a one-to-many relationship with the type Synonym.
     *
     * @return void
     */
    public function test_synonyms(): void
    {
        $synonymCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(AnimeSynonym::factory()->count($synonymCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $anime->animesynonyms());
        static::assertEquals($synonymCount, $anime->animesynonyms()->count());
        static::assertInstanceOf(AnimeSynonym::class, $anime->animesynonyms()->first());
    }

    /**
     * Anime shall have a many-to-many relationship with the type Series.
     *
     * @return void
     */
    public function test_series(): void
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
    public function test_themes(): void
    {
        $themeCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(AnimeTheme::factory()->count($themeCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $anime->animethemes());
        static::assertEquals($themeCount, $anime->animethemes()->count());
        static::assertInstanceOf(AnimeTheme::class, $anime->animethemes()->first());
    }

    /**
     * Anime shall have a many-to-many relationship with the type ExternalResource.
     *
     * @return void
     */
    public function test_external_resources(): void
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
    public function test_images(): void
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

    /**
     * Anime shall have a many-to-many relationship with the type Studio.
     *
     * @return void
     */
    public function test_studios(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Studio::factory()->count($studioCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $anime->studios());
        static::assertEquals($studioCount, $anime->studios()->count());
        static::assertInstanceOf(Studio::class, $anime->studios()->first());
        static::assertEquals(AnimeStudio::class, $anime->studios()->getPivotClass());
    }
}
