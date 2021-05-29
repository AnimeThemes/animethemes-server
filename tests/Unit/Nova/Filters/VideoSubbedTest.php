<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoSubbedFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoSubbedTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

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
        $subbedFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(VideoSubbedFilter::class);

        $response = $filter->apply(Video::class, $subbedFilter);

        $filteredVideos = Video::where('subbed', $subbedFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
