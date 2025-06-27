<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class AnimeThemeTest extends TestCase
{
    use WithFaker;

    /**
     * The type attribute of a theme shall be cast to a ThemeType enum instance.
     *
     * @return void
     */
    public function test_casts_type_to_enum(): void
    {
        $theme = AnimeTheme::factory()
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
    public function test_searchable_as(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($theme->searchableAs());
    }

    /**
     * Theme shall be a searchable resource.
     *
     * @return void
     */
    public function test_to_searchable_array(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsArray($theme->toSearchableArray());
    }

    /**
     * Themes shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($theme->getName());
    }

    /**
     * Themes shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($theme->getSubtitle());
    }

    /**
     * Themes shall belong to an Anime.
     *
     * @return void
     */
    public function test_anime(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $theme->anime());
        static::assertInstanceOf(Anime::class, $theme->anime()->first());
    }

    /**
     * Themes shall belong to a Group.
     *
     * @return void
     */
    public function test_group(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->for(Group::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $theme->group());
        static::assertInstanceOf(Group::class, $theme->group()->first());
    }

    /**
     * Themes shall belong to a Song.
     *
     * @return void
     */
    public function test_song(): void
    {
        $theme = AnimeTheme::factory()
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
    public function test_entries(): void
    {
        $entryCount = $this->faker->randomDigitNotNull();

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($entryCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $theme->animethemeentries());
        static::assertEquals($entryCount, $theme->animethemeentries()->count());
        static::assertInstanceOf(AnimeThemeEntry::class, $theme->animethemeentries()->first());
    }

    /**
     * Themes shall have a generated slug on creation.
     *
     * @return void
     */
    public function test_theme_creates_slug(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertArrayHasKey('slug', $theme);
    }
}
