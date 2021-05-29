<?php

namespace Tests\Unit\Models;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use App\Pivots\VideoEntry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

class EntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

        $this->assertIsString($entry->searchableAs());
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

        $this->assertIsArray($entry->toSearchableArray());
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

        $this->assertEquals(1, $entry->audits->count());
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

        $this->assertIsString($entry->getName());
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

        $this->assertInstanceOf(BelongsTo::class, $entry->theme());
        $this->assertInstanceOf(Theme::class, $entry->theme()->first());
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

        $this->assertInstanceOf(BelongsToMany::class, $entry->videos());
        $this->assertEquals($videoCount, $entry->videos()->count());
        $this->assertInstanceOf(Video::class, $entry->videos()->first());
        $this->assertEquals(VideoEntry::class, $entry->videos()->getPivotClass());
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

        $this->assertInstanceOf(BelongsToThrough::class, $entry->anime());
        $this->assertInstanceOf(Anime::class, $entry->anime()->first());
    }
}
