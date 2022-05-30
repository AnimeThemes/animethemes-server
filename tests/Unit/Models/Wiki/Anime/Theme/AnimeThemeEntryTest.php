<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime\Theme;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class EntryTest.
 */
class AnimeThemeEntryTest extends TestCase
{
    use WithFaker;

    /**
     * Entry shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsString($entry->searchableAs());
    }

    /**
     * Entry shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsArray($entry->toSearchableArray());
    }

    /**
     * Entries shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertEquals(1, $entry->audits()->count());
    }

    /**
     * Entries shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsString($entry->getName());
    }

    /**
     * Entries shall belong to a Theme.
     *
     * @return void
     */
    public function testTheme(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $entry->animetheme());
        static::assertInstanceOf(AnimeTheme::class, $entry->animetheme()->first());
    }

    /**
     * Entries shall have a many-to-many relationship with the type Video.
     *
     * @return void
     */
    public function testVideos(): void
    {
        $videoCount = $this->faker->randomDigitNotNull();

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($videoCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $entry->videos());
        static::assertEquals($videoCount, $entry->videos()->count());
        static::assertInstanceOf(Video::class, $entry->videos()->first());
        static::assertEquals(AnimeThemeEntryVideo::class, $entry->videos()->getPivotClass());
    }

    /**
     * Entries shall belong to an Anime through a Theme.
     *
     * @return void
     */
    public function testAnime(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertInstanceOf(BelongsToThrough::class, $entry->anime());
        static::assertInstanceOf(Anime::class, $entry->anime()->first());
    }
}
