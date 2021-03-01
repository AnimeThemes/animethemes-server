<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoNcFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoNcTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Video Nc Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoNcFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Nc Filter shall have Yes and No options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoNcFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Nc Filter shall filter Videos By Nc.
     *
     * @return void
     */
    public function testFilter()
    {
        $nc_filter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(VideoNcFilter::class);

        $response = $filter->apply(Video::class, $nc_filter);

        $filtered_videos = Video::where('nc', $nc_filter)->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }
}
