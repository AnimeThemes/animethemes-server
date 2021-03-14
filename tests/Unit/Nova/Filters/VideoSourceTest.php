<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\VideoSource;
use App\Models\Video;
use App\Nova\Filters\VideoSourceFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoSourceTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Video Source Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoSourceFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Source Filter shall have an option for each VideoSource instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoSourceFilter::class);

        foreach (VideoSource::getInstances() as $source) {
            $filter->assertHasOption($source->description);
        }
    }

    /**
     * The Video Source Filter shall filter Video By Source.
     *
     * @return void
     */
    public function testFilter()
    {
        $source = VideoSource::getRandomInstance();

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(VideoSourceFilter::class);

        $response = $filter->apply(Video::class, $source->value);

        $filtered_videos = Video::where('source', $source->value)->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }
}
