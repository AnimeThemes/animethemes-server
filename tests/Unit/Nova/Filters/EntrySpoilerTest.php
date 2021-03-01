<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Nova\Filters\EntrySpoilerFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class EntrySpoilerTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Entry Spoiler Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(EntrySpoilerFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Entry Spoiler Filter shall have an option for each AnimeSeason instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(EntrySpoilerFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Entry Spoiler Filter shall filter Entries By Spoiler.
     *
     * @return void
     */
    public function testFilter()
    {
        $spoiler_filter = $this->faker->boolean();

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(EntrySpoilerFilter::class);

        $response = $filter->apply(Entry::class, $spoiler_filter);

        $filtered_entries = Entry::where('spoiler', $spoiler_filter)->get();
        foreach ($filtered_entries as $filtered_entry) {
            $response->assertContains($filtered_entry);
        }
        $response->assertCount($filtered_entries->count());
    }
}
