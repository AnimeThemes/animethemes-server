<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoSubbedFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoSubbedTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Video Subbed Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoSubbedFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Subbed Filter shall have Yes and No options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoSubbedFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Subbed Filter shall filter Videos By Subbed.
     *
     * @return void
     */
    public function testFilter()
    {
        $subbed_filter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(VideoSubbedFilter::class);

        $response = $filter->apply(Video::class, $subbed_filter);

        $filtered_videos = Video::where('subbed', $subbed_filter)->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }
}
