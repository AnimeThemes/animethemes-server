<?php declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoSubbedFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoSubbedTest
 * @package Nova\Filters
 */
class VideoSubbedTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Subbed Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoSubbedFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Subbed Filter shall have Yes and No options.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoSubbedFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Subbed Filter shall filter Videos By Subbed.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $subbedFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = static::novaFilter(VideoSubbedFilter::class);

        $response = $filter->apply(Video::class, $subbedFilter);

        $filteredVideos = Video::where('subbed', $subbedFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
