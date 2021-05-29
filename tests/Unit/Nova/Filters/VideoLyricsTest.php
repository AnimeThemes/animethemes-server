<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoLyricsFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoLyricsTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Video Lyrics Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoLyricsFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Lyrics Filter shall have Yes and No options.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoLyricsFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Lyrics Filter shall filter Videos By Lyrics.
     *
     * @return void
     */
    public function testFilter()
    {
        $lyricsFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(VideoLyricsFilter::class);

        $response = $filter->apply(Video::class, $lyricsFilter);

        $filteredVideos = Video::where('lyrics', $lyricsFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
