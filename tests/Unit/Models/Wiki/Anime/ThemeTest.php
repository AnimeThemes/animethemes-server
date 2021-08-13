<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime;

use App\Enums\Models\Wiki\Anime\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Anime\Theme;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class ThemeTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * The type attribute of a theme shall be cast to a ThemeType enum instance.
     *
     * @return void
     */
    public function testCastsTypeToEnum()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        $type = $theme->type;

        static::assertInstanceOf(ThemeType::class, $type);
    }

    /**
     * Theme shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($theme->searchableAs());
    }

    /**
     * Theme shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsArray($theme->toSearchableArray());
    }

    /**
     * Themes shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertEquals(1, $theme->audits()->count());
    }

    /**
     * Themes shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($theme->getName());
    }

    /**
     * Themes shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $theme->anime());
        static::assertInstanceOf(Anime::class, $theme->anime()->first());
    }

    /**
     * Themes shall belong to a Song.
     *
     * @return void
     */
    public function testSong()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $theme->song());
        static::assertInstanceOf(Song::class, $theme->song()->first());
    }

    /**
     * Theme shall have a one-to-many relationship with the type Entry.
     *
     * @return void
     */
    public function testEntries()
    {
        $entryCount = $this->faker->randomDigitNotNull();

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($entryCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $theme->entries());
        static::assertEquals($entryCount, $theme->entries()->count());
        static::assertInstanceOf(Entry::class, $theme->entries()->first());
    }

    /**
     * Themes shall have a generated slug on creation.
     *
     * @return void
     */
    public function testThemeCreatesSlug()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertArrayHasKey('slug', $theme);
    }
}
