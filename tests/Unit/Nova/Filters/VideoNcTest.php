<?php declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoNcFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoNcTest
 * @package Nova\Filters
 */
class VideoNcTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Nc Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoNcFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Nc Filter shall have Yes and No options.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoNcFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Nc Filter shall filter Videos By Nc.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $ncFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = static::novaFilter(VideoNcFilter::class);

        $response = $filter->apply(Video::class, $ncFilter);

        $filteredVideos = Video::where('nc', $ncFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
