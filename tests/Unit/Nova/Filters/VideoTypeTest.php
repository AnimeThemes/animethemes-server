<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoTypeFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class VideoTypeTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Video Type Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(VideoTypeFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Type Filter shall have an Anime and Misc option.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(VideoTypeFilter::class);

        $filter->assertHasOption(__('nova.anime'));
        $filter->assertHasOption(__('nova.misc'));
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     */
    public function testAnimeFilter()
    {
        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = $this->novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::ANIME);

        $filtered_videos = Video::where('path', 'not like', 'misc%')->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     */
    public function testMiscFilter()
    {
        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = $this->novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::MISC);

        $filtered_videos = Video::where('path', 'like', 'misc%')->get();
        foreach ($filtered_videos as $filtered_video) {
            $response->assertContains($filtered_video);
        }
        $response->assertCount($filtered_videos->count());
    }
}
