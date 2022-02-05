<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Video;

use App\Models\Wiki\Video;
use App\Nova\Filters\Wiki\Video\VideoLyricsFilter;
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
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Lyrics Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(VideoLyricsFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Lyrics Filter shall have Yes and No options.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(VideoLyricsFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Lyrics Filter shall filter Videos By Lyrics.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter(): void
    {
        $lyricsFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(VideoLyricsFilter::class);

        $response = $filter->apply(Video::class, $lyricsFilter);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
