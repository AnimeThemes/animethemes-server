<?php

declare(strict_types=1);

namespace Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Pivots\VideoEntry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class EntryTest.
 */
class EntryTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Entry shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertIsString($entry->searchableAs());
    }

    /**
     * Entry shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertIsArray($entry->toSearchableArray());
    }

    /**
     * Entries shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertEquals(1, $entry->audits->count());
    }

    /**
     * Entries shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertIsString($entry->getName());
    }

    /**
     * Entries shall belong to a Theme.
     *
     * @return void
     */
    public function testTheme()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertInstanceOf(BelongsTo::class, $entry->theme());
        static::assertInstanceOf(Theme::class, $entry->theme()->first());
    }

    /**
     * Entries shall have a many-to-many relationship with the type Video.
     *
     * @return void
     */
    public function testVideos()
    {
        $videoCount = $this->faker->randomDigitNotNull;

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($videoCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $entry->videos());
        static::assertEquals($videoCount, $entry->videos()->count());
        static::assertInstanceOf(Video::class, $entry->videos()->first());
        static::assertEquals(VideoEntry::class, $entry->videos()->getPivotClass());
    }

    /**
     * Entries shall belong to an Anime through a Theme.
     *
     * @return void
     */
    public function testAnime()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        static::assertInstanceOf(BelongsToThrough::class, $entry->anime());
        static::assertInstanceOf(Anime::class, $entry->anime()->first());
    }
}
