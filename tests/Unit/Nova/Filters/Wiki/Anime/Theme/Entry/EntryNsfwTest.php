<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Anime\Theme\Entry;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Nova\Filters\Wiki\Anime\Theme\Entry\EntryNsfwFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class EntryNsfwTest.
 */
class EntryNsfwTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Entry Nsfw Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(EntryNsfwFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Entry Nsfw Filter shall have Yes and No options.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(EntryNsfwFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Entry Nsfw Filter shall filter Entries By NSFW.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $nsfwFilter = $this->faker->boolean();

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(EntryNsfwFilter::class);

        $response = $filter->apply(AnimeThemeEntry::class, $nsfwFilter);

        $filteredEntries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter)->get();
        foreach ($filteredEntries as $filteredEntry) {
            $response->assertContains($filteredEntry);
        }
        $response->assertCount($filteredEntries->count());
    }
}
