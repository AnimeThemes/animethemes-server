<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Video;

use App\Models\Wiki\Video;
use App\Nova\Filters\Wiki\Video\VideoUncenFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoUncenTest.
 */
class VideoUncenTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Uncen Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(VideoUncenFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Uncen Filter shall have Yes and No options.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(VideoUncenFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Uncen Filter shall filter Videos By Uncen.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter(): void
    {
        $uncenFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(VideoUncenFilter::class);

        $response = $filter->apply(Video::class, $uncenFilter);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_UNCEN, $uncenFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
