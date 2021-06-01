<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoUncenFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoUncenTest
 * @package Nova\Filters
 */
class VideoUncenTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Uncen Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoUncenFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Uncen Filter shall have Yes and No options.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoUncenFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Uncen Filter shall filter Videos By Uncen.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $uncenFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = static::novaFilter(VideoUncenFilter::class);

        $response = $filter->apply(Video::class, $uncenFilter);

        $filteredVideos = Video::where('uncen', $uncenFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
