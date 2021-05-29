<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\VideoOverlap;
use App\Models\Video;
use App\Nova\Filters\VideoOverlapFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoOverlapTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Video Overlap Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoOverlapFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Overlap Filter shall have an option for each VideoOverlap instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoOverlapFilter::class);

        foreach (VideoOverlap::getInstances() as $overlap) {
            $filter->assertHasOption($overlap->description);
        }
    }

    /**
     * The Video Overlap Filter shall filter Video By Overlap.
     *
     * @return void
     */
    public function testFilter()
    {
        $overlap = VideoOverlap::getRandomInstance();

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(VideoOverlapFilter::class);

        $response = $filter->apply(Video::class, $overlap->value);

        $filteredVideos = Video::where('overlap', $overlap->value)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
