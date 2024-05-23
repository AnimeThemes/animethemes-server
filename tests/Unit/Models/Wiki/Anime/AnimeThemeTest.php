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
    public function testCastsTypeToEnum(): void
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
    public function testSearchableAs(): void
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
    public function testToSearchableArray(): void
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
    public function testNameable(): void
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
    public function testHasSubtitle(): void
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
    public function testAnime(): void
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
    public function testGroup(): void
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
    public function testSong(): void
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
    public function testEntries(): void
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
    public function testThemeCreatesSlug(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertArrayHasKey('slug', $theme);
    }
}
