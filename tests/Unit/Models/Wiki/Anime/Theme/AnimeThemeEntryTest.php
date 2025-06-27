<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime\Theme;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function test_searchable_as(): void
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
    public function test_to_searchable_array(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsArray($entry->toSearchableArray());
    }

    /**
     * Entries shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsString($entry->getName());
    }

    /**
     * Entries shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertIsString($entry->getSubtitle());
    }

    /**
     * Entries shall belong to a Theme.
     *
     * @return void
     */
    public function test_theme(): void
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
    public function test_videos(): void
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
    public function test_anime(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        static::assertInstanceOf(BelongsToThrough::class, $entry->anime());
        static::assertInstanceOf(Anime::class, $entry->anime()->first());
    }
}
