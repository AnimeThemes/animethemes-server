<?php

namespace Tests\Unit\Models;

use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The type attribute of a theme shall be cast to a ThemeType enum instance.
     *
     * @return void
     */
    public function testCastsTypeToEnum()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $type = $theme->type;

        $this->assertInstanceOf(ThemeType::class, $type);
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
            ->create();

        $this->assertIsString($theme->searchableAs());
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
            ->create();

        $this->assertIsArray($theme->toSearchableArray());
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
            ->create();

        $this->assertEquals(1, $theme->audits->count());
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
            ->create();

        $this->assertIsString($theme->getName());
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
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $theme->anime());
        $this->assertInstanceOf(Anime::class, $theme->anime()->first());
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
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $theme->song());
        $this->assertInstanceOf(Song::class, $theme->song()->first());
    }

    /**
     * Theme shall have a one-to-many relationship with the type Entry.
     *
     * @return void
     */
    public function testEntries()
    {
        $entryCount = $this->faker->randomDigitNotNull;

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($entryCount))
            ->create();

        $this->assertInstanceOf(HasMany::class, $theme->entries());
        $this->assertEquals($entryCount, $theme->entries()->count());
        $this->assertInstanceOf(Entry::class, $theme->entries()->first());
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
            ->create();

        $this->assertArrayHasKey('slug', $theme);
    }
}
