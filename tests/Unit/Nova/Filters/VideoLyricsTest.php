<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Wiki\Video;
use App\Nova\Filters\VideoLyricsFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoLyricsTest.
 */
class VideoLyricsTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Lyrics Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoLyricsFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Lyrics Filter shall have Yes and No options.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoLyricsFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Lyrics Filter shall filter Videos By Lyrics.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $lyricsFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = static::novaFilter(VideoLyricsFilter::class);

        $response = $filter->apply(Video::class, $lyricsFilter);

        $filteredVideos = Video::where('lyrics', $lyricsFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
