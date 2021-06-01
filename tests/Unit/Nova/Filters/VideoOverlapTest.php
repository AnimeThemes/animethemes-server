<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Enums\VideoOverlap;
use App\Models\Video;
use App\Nova\Filters\VideoOverlapFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoOverlapTest.
 */
class VideoOverlapTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Overlap Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoOverlapFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Overlap Filter shall have an option for each VideoOverlap instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoOverlapFilter::class);

        foreach (VideoOverlap::getInstances() as $overlap) {
            $filter->assertHasOption($overlap->description);
        }
    }

    /**
     * The Video Overlap Filter shall filter Video By Overlap.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $overlap = VideoOverlap::getRandomInstance();

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = static::novaFilter(VideoOverlapFilter::class);

        $response = $filter->apply(Video::class, $overlap->value);

        $filteredVideos = Video::where('overlap', $overlap->value)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
