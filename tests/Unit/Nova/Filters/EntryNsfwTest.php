<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Nova\Filters\EntryNsfwFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class EntryNsfwTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Entry Nsfw Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(EntryNsfwFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Entry Nsfw Filter shall have Yes and No options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(EntryNsfwFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Entry Nsfw Filter shall filter Entries By NSFW.
     *
     * @return void
     */
    public function testFilter()
    {
        $nsfw_filter = $this->faker->boolean();

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(EntryNsfwFilter::class);

        $response = $filter->apply(Entry::class, $nsfw_filter);

        $filtered_entries = Entry::where('nsfw', $nsfw_filter)->get();
        foreach ($filtered_entries as $filtered_entry) {
            $response->assertContains($filtered_entry);
        }
        $response->assertCount($filtered_entries->count());
    }
}
