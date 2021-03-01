<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoUncenFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoUncenTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Video Uncen Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoUncenFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Uncen Filter shall have Yes and No options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoUncenFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Uncen Filter shall filter Videos By Uncen.
     *
     * @return void
     */
    public function testFilter()
    {
        $uncen_filter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(VideoUncenFilter::class);

        $response = $filter->apply(Video::class, $uncen_filter);

        $filtered_videos = Video::where('uncen', $uncen_filter)->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }
}
