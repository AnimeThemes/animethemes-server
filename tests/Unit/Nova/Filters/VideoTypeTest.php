<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Video;
use App\Nova\Filters\VideoTypeFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoTypeTest.
 */
class VideoTypeTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Type Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(VideoTypeFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Type Filter shall have an Anime and Misc option.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(VideoTypeFilter::class);

        $filter->assertHasOption(__('nova.anime'));
        $filter->assertHasOption(__('nova.misc'));
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testAnimeFilter()
    {
        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = static::novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::ANIME);

        $filteredVideos = Video::where('path', 'not like', 'misc%')->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testMiscFilter()
    {
        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = static::novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::MISC);

        $filteredVideos = Video::where('path', 'like', 'misc%')->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
