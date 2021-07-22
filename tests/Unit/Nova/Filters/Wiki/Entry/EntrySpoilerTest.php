<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Entry;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Nova\Filters\Wiki\Entry\EntrySpoilerFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class EntrySpoilerTest.
 */
class EntrySpoilerTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Entry Spoiler Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(EntrySpoilerFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Entry Spoiler Filter shall have an option for each AnimeSeason instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(EntrySpoilerFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Entry Spoiler Filter shall filter Entries By Spoiler.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $spoilerFilter = $this->faker->boolean();

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(EntrySpoilerFilter::class);

        $response = $filter->apply(Entry::class, $spoilerFilter);

        $filteredEntries = Entry::query()->where('spoiler', $spoilerFilter)->get();
        foreach ($filteredEntries as $filteredEntry) {
            $response->assertContains($filteredEntry);
        }
        $response->assertCount($filteredEntries->count());
    }
}